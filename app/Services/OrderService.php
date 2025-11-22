<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\Bundle;
use App\Models\QrCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * Create a new order
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Calculate pricing
            $items = $data['items'];
            $subtotal = $this->calculateSubtotal($items);

            $discountAmount = $data['discount_amount'] ?? 0;
            $taxAmount = $data['tax_amount'] ?? 0;
            $shippingAmount = $data['shipping_amount'] ?? 0;

            $total = $subtotal - $discountAmount + $taxAmount + $shippingAmount;
            // Create order
            $order = Order::create([
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'customer_address' => $data['customer_address'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => $data['payment_status'] ?? 'pending',
                'payment_method' => $data['payment_method'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            // Create order items and deduct stock
            foreach ($items as $item) {
                $this->createOrderItem($order, $item);
                $this->deductStock($item['item_type'], $item['item_id'], $item['quantity']);
            }

            // Log initial status
            $this->logStatusChange($order, null, 'pending', 'Order created');

            return $order->load('items');
        });
    }

    /**
     * Update order
     */
    public function update(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $oldStatus = $order->status;

            $order->update(array_filter([
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'payment_status' => $data['payment_status'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'status' => $data['status'] ?? null,
                'tracking_number' => $data['tracking_number'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
            ], function ($value) {
                return !is_null($value);
            }));

            // Log status change if status was updated
            if (isset($data['status']) && $oldStatus !== $data['status']) {
                $this->logStatusChange($order, $oldStatus, $data['status']);
            }

            return $order->fresh(['items']);
        });
    }

    /**
     * Delete order
     */
    public function delete(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            // Return stock if order is being deleted (pending or processing orders only)
            if (in_array($order->status, ['pending', 'processing'])) {
                foreach ($order->items as $item) {
                    $this->returnStock($item->item_type, $item->item_id, $item->quantity);
                }
            }

            // Unassign all QR codes
            foreach ($order->items as $item) {
                if ($item->qr_code_id) {
                    $item->unassignQRCode();
                }
            }

            return $order->delete();
        });
    }

    /**
     * Deduct stock when order is created
     */
    private function deductStock(string $itemType, int $itemId, int $quantity): void
    {
        if ($itemType === 'product') {
            $product = Product::findOrFail($itemId);

            if ($product->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock for product '{$product->name}'. Available: {$product->stock_quantity}, Required: {$quantity}");
            }

            $product->decrement('stock_quantity', $quantity);
            $product->updateStockStatus();
            $product->save();
        } elseif ($itemType === 'bundle') {
            $bundle = Bundle::findOrFail($itemId);

            if ($bundle->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock for bundle '{$bundle->name}'. Available: {$bundle->stock_quantity}, Required: {$quantity}");
            }

            // Only decrement bundle quantity, NOT products
            $bundle->decrement('stock_quantity', $quantity);
        }
    }

    /**
     * Return stock when order is cancelled/refunded or return is completed
     */
    private function returnStock(string $itemType, int $itemId, int $quantity): void
    {
        if ($itemType === 'product') {
            $product = Product::find($itemId);
            if ($product) {
                $product->increment('stock_quantity', $quantity);
                $product->updateStockStatus();
                $product->save();
            }
        } elseif ($itemType === 'bundle') {
            $bundle = Bundle::find($itemId);
            if ($bundle) {
                // Only increment bundle quantity, NOT products
                $bundle->increment('stock_quantity', $quantity);
            }
        }
    }

    /**
     * Assign QR codes to order items
     */
public function assignQRCodes(Order $order, array $assignments): Order
{
    return DB::transaction(function () use ($order, $assignments) {
        foreach ($assignments as $assignment) {
            // Skip if no QR code provided
            if (empty($assignment['qr_code']) && empty($assignment['qr_code_id'])) {
                continue;
            }

            $orderItem = OrderItem::find($assignment['order_item_id']);
            
            // Find QR code by code or ID
            if (!empty($assignment['qr_code'])) {
                $qrCode = QrCode::where('code', $assignment['qr_code'])->first();
            } else {
                $qrCode = QrCode::find($assignment['qr_code_id']);
            }

            if (!$qrCode) {
                throw new \Exception("QR Code not found: " . ($assignment['qr_code'] ?? $assignment['qr_code_id']));
            }

            if ($orderItem && $orderItem->order_id === $order->id) {
                // Check if QR code is active
                if ($qrCode->status !== 'active') {
                    throw new \Exception("QR Code {$qrCode->code} is not active.");
                }

                // Check if QR code is expired
                if ($qrCode->isExpired()) {
                    throw new \Exception("QR Code {$qrCode->code} has expired.");
                }

                // Check if QR already assigned to another order item
                $existingAssignment = OrderItem::where('qr_code_id', $qrCode->id)
                    ->where('id', '!=', $orderItem->id)
                    ->first();

                if ($existingAssignment) {
                    $existingOrder = $existingAssignment->order;
                    throw new \Exception("QR Code {$qrCode->code} is already assigned to Order #{$existingOrder->order_number}.");
                }

                // If reassigning, unassign old QR code first
                if (isset($assignment['reassign']) && $assignment['reassign']) {
                    if ($orderItem->qr_code_id) {
                        $orderItem->unassignQRCode();
                    }
                }

                // Assign QR code
                $orderItem->assignQRCode($qrCode->id);
            }
        }

        return $order->fresh(['items', 'items.qrCode']);
    });
}

    /**
     * Unassign QR code from order item
     */
    public function unassignQRCode(OrderItem $orderItem): void
    {
        $orderItem->unassignQRCode();
    }

    /**
     * Change order status
     */
    public function changeStatus(Order $order, string $newStatus, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $notes) {
            $oldStatus = $order->status;

            // Validate status transition
            $this->validateStatusTransition($order, $newStatus);

            // Update order
            $updateData = ['status' => $newStatus];

            // If changing to refunded, initialize return_status to pending
            if ($newStatus === 'refunded' && !$order->return_status) {
                $updateData['return_status'] = 'pending';
            }

            // Set processed_at when moving to processing
            if ($newStatus === 'processing' && !$order->processed_at) {
                $updateData['processed_by'] = Auth::id();
                $updateData['processed_at'] = now();
            }

            // Set shipped_at when moving to shipped
            if ($newStatus === 'shipped' && !$order->shipped_at) {
                $updateData['shipped_at'] = now();
            }

            // Set delivered_at when moving to delivered
            if ($newStatus === 'delivered' && !$order->delivered_at) {
                $updateData['delivered_at'] = now();
            }

            $order->update($updateData);

            // Log status change
            $this->logStatusChange($order, $oldStatus, $newStatus, $notes);

            return $order->fresh();
        });
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Order $order, string $paymentStatus): Order
    {
        $oldPaymentStatus = $order->payment_status;

        $order->update(['payment_status' => $paymentStatus]);

        $this->logStatusChange(
            $order,
            $oldPaymentStatus,
            $paymentStatus,
            "Payment status updated from {$oldPaymentStatus} to {$paymentStatus}"
        );

        return $order->fresh();
    }

    /**
     * Update return status and handle stock return
     */
    public function updateReturnStatus(Order $order, string $returnStatus, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($order, $returnStatus, $notes) {
            // Validate order is refunded
            if ($order->status !== 'refunded') {
                throw new \Exception('Return status can only be changed for refunded orders.');
            }

            $oldReturnStatus = $order->return_status;

            // Update return status
            $order->update(['return_status' => $returnStatus]);

            // If return is completed, return stock to inventory
            if ($returnStatus === 'completed' && $oldReturnStatus !== 'completed') {
                foreach ($order->items as $item) {
                    $this->returnStock($item->item_type, $item->item_id, $item->quantity);
                }
            }

            // Log the change
            $this->logStatusChange(
                $order,
                $oldReturnStatus ?? 'none',
                $returnStatus,
                $notes ?? "Return status changed from " . ($oldReturnStatus ?? 'none') . " to {$returnStatus}"
            );

            return $order->fresh();
        });
    }

    /**
     * Calculate subtotal from items
     */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            if ($item['item_type'] === 'product') {
                $product = Product::find($item['item_id']);
                if ($product) {
                    $subtotal += $product->final_price * $item['quantity'];
                }
            } elseif ($item['item_type'] === 'bundle') {
                $bundle = Bundle::find($item['item_id']);
                if ($bundle) {
                    $subtotal += $bundle->final_price * $item['quantity'];
                }
            }
        }

        return $subtotal;
    }

    /**
     * Create order item
     */
    private function createOrderItem(Order $order, array $itemData): OrderItem
    {
        $itemType = $itemData['item_type'];
        $itemId = $itemData['item_id'];
        $quantity = $itemData['quantity'];

        // Get item details
        if ($itemType === 'product') {
            $product = Product::findOrFail($itemId);
            $itemName = $product->name;
            $itemDescription = $product->description;
            $unitPrice = $product->final_price;
        } else { // bundle
            $bundle = Bundle::findOrFail($itemId);
            $itemName = $bundle->name;
            $itemDescription = $bundle->description;
            $unitPrice = $bundle->final_price;
        }

        $total = $unitPrice * $quantity;

        return OrderItem::create([
            'order_id' => $order->id,
            'item_type' => $itemType,
            'item_id' => $itemId,
            'item_name' => $itemName,
            'item_description' => $itemDescription,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'discount_amount' => 0,
            'total' => $total,
        ]);
    }

    /**
     * Log status change
     */
    private function logStatusChange(Order $order, ?string $oldStatus, string $newStatus, ?string $notes = null): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'changed_by' => Auth::id(),
            'changed_at' => now(),
        ]);
    }

    /**
     * Validate status transition
     */
    private function validateStatusTransition(Order $order, string $newStatus): void
    {
        $currentStatus = $order->status;

        // Can't move to processing without all QR codes assigned
        if ($newStatus === 'processing' && !$order->hasAllQRCodesAssigned()) {
            throw new \Exception('Cannot move to processing: Not all items have QR codes assigned.');
        }

        // Can't move to processing without payment
        if ($newStatus === 'processing' && $order->payment_status !== 'paid') {
            throw new \Exception('Cannot move to processing: Order payment is not completed.');
        }

        // Can't move backwards (except to cancelled/refunded)
        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
        $currentIndex = array_search($currentStatus, $statusOrder);
        $newIndex = array_search($newStatus, $statusOrder);

        if ($currentIndex !== false && $newIndex !== false && $newIndex < $currentIndex) {
            if (!in_array($newStatus, ['cancelled', 'refunded'])) {
                throw new \Exception('Cannot move order backwards in status.');
            }
        }
    }

    /**
     * Get available QR codes for assignment
     */
    public function getAvailableQRCodes()
    {
        return QrCode::active()
            ->whereDoesntHave('orderItems')
            ->orderBy('code')
            ->get();
    }

    /**
     * Get order statistics
     */
    public function getStatistics(Order $order): array
    {
        return [
            'total_items' => $order->items->count(),
            'total_quantity' => $order->items->sum('quantity'),
            'assigned_qr_codes' => $order->getAssignedQRCodesCount(),
            'pending_qr_codes' => $order->getTotalItemsCount() - $order->getAssignedQRCodesCount(),
            'all_qr_assigned' => $order->hasAllQRCodesAssigned(),
            'can_process' => $order->canAssignQRCodes(),
            'has_pending_return' => $order->hasPendingReturn(),
            'is_return_completed' => $order->isReturnCompleted(),
            'can_change_return_status' => $order->canChangeReturnStatus(),
        ];
    }
}
