<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Bundle;
use App\Services\OrderService;
use App\Http\Requests\Business\StoreOrderRequest;
use App\Http\Requests\Business\UpdateOrderRequest;
use App\Http\Requests\Business\AssignQRCodesRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['items', 'processedBy'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::active()->get();
        $bundles = Bundle::active()->get();
        
        return view('orders.create', compact('products', 'bundles'));
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->create($request->validated());

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load(['items.qrCode', 'statusHistory.changedBy', 'processedBy']);
        
        // Manually load products and bundles for each item based on type
        foreach ($order->items as $item) {
            if ($item->item_type === 'product') {
                $item->load('product');
            } elseif ($item->item_type === 'bundle') {
                $item->load('bundle');
            }
        }
        
        $statistics = $this->orderService->getStatistics($order);
        
        return view('orders.show', compact('order', 'statistics'));
    }

    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            $order = $this->orderService->update($order, $request->validated());

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        try {
            $this->orderService->delete($order);

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Show QR code assignment page
     */
    public function assignQRCodesPage(Order $order)
    {
        if (!$order->canAssignQRCodes()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Cannot assign QR codes. Order must be pending and paid.');
        }

        $order->load(['items.qrCode']);
        $availableQRCodes = $this->orderService->getAvailableQRCodes();

        return view('orders.assign-qr-codes', compact('order', 'availableQRCodes'));
    }

    /**
     * Assign QR codes to order items
     */
    public function assignQRCodes(AssignQRCodesRequest $request, Order $order)
    {
        try {
            $this->orderService->assignQRCodes($order, $request->qr_assignments);

            return redirect()
                ->route('orders.assign-qr-codes', $order)
                ->with('success', 'QR codes assigned successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to assign QR codes: ' . $e->getMessage());
        }
    }

    /**
     * Unassign QR code from order item
     */
    public function unassignQRCode(Order $order, $orderItemId)
    {
        try {
            $orderItem = $order->items()->findOrFail($orderItemId);
            $this->orderService->unassignQRCode($orderItem);

            return response()->json([
                'success' => true,
                'message' => 'QR code unassigned successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unassign QR code: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change order status
     */
    public function changeStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->orderService->changeStatus($order, $request->status, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        try {
            $this->orderService->updatePaymentStatus($order, $request->payment_status);

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}