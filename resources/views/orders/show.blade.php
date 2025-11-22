<x-dashboard-layout>
    <x-slot name="title">Order {{ $order->order_number }}</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('orders.index') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Orders
            </a>

            <div class="flex items-center gap-3">
                @if ($order->canAssignQRCodes())
                    <a href="{{ route('orders.assign-qr-codes', $order) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Assign QR Codes
                    </a>
                @endif

                <a href="{{ route('orders.edit', $order) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Order
                </a>
            </div>
        </div>

        <!-- Order Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">{{ $order->order_number }}</h1>
                    <p class="text-sm text-gray-600">Created {{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <span
                        class="inline-flex items-center px-4 py-2 {{ $order->status_badge_class }} text-sm font-bold rounded-lg">
                        {{ $order->status_label }}
                    </span>
                    <span
                        class="inline-flex items-center px-4 py-2 {{ $order->payment_status_badge_class }} text-sm font-bold rounded-lg">
                        {{ $order->payment_status_label }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="border-b border-gray-200 bg-gray-50 p-6">
                        <h2 class="text-lg font-bold text-gray-900">Order Items ({{ $order->items->count() }})</h2>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach ($order->items as $item)
                            <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="font-bold text-gray-900">{{ $item->item_name }}</h3>
                                            <span
                                                class="inline-block px-2 py-0.5 bg-gray-200 text-gray-700 text-xs font-medium rounded mt-1">
                                                {{ $item->item_type_label }}
                                            </span>
                                        </div>
                                        <span class="text-lg font-black text-gray-900">EGP
                                            {{ number_format($item->total, 2) }}</span>
                                    </div>

                                    @if ($item->item_description)
                                        <p class="text-sm text-gray-600 mb-3">{{ $item->item_description }}</p>
                                    @endif

                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                        <span>Qty: <strong class="text-gray-900">{{ $item->quantity }}</strong></span>
                                        <span>Unit Price: <strong class="text-gray-900">EGP
                                                {{ number_format($item->unit_price, 2) }}</strong></span>
                                    </div>

                                    <!-- QR Code Assignment Status -->
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        @if ($item->qrCode)
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-green-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span class="text-sm font-semibold text-green-800">QR Code:
                                                        {{ $item->qrCode->code }}</span>
                                                </div>
                                                <a href="{{ route('qr-codes.show', $item->qrCode) }}" target="_blank"
                                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                    View QR →
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-yellow-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-sm font-semibold">No QR Code Assigned</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status History -->
                @if ($order->statusHistory->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="border-b border-gray-200 bg-gray-50 p-6">
                            <h2 class="text-lg font-bold text-gray-900">Status History</h2>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach ($order->statusHistory as $history)
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-2 h-2 mt-2 bg-red-500 rounded-full"></div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $history->status_change_text }}
                                                </p>
                                                <span
                                                    class="text-xs text-gray-500">{{ $history->changed_at->format('M d, Y h:i A') }}</span>
                                            </div>
                                            @if ($history->notes)
                                                <p class="text-sm text-gray-600 mt-1">{{ $history->notes }}</p>
                                            @endif
                                            @if ($history->changedBy)
                                                <p class="text-xs text-gray-500 mt-1">by
                                                    {{ $history->changedBy->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- QR Assignment Status -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">QR Assignment</h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Items:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $statistics['total_items'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Assigned QR Codes:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $statistics['assigned_qr_codes'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Pending QR Codes:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $statistics['pending_qr_codes'] }}</span>
                        </div>

                        @if ($statistics['all_qr_assigned'])
                            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm font-semibold text-green-800 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    All QR Codes Assigned
                                </p>
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm font-semibold text-yellow-800">Pending QR Assignment</p>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Return Status (if applicable) -->
                @if ($order->status === 'refunded')
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Return Status</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Current Status:</span>
                                <span
                                    class="inline-flex items-center px-3 py-1 {{ $order->return_status_badge_class }} text-xs font-bold rounded-lg">
                                    {{ $order->return_status_label }}
                                </span>
                            </div>

                            @if ($statistics['has_pending_return'])
                                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm font-semibold text-yellow-800">Return is pending approval</p>
                                </div>
                            @elseif($statistics['is_return_completed'])
                                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-sm font-semibold text-green-800">Return completed - Stock returned
                                    </p>
                                </div>
                            @endif

                            @if ($statistics['can_change_return_status'] && $order->return_status !== 'completed')
                                <div class="space-y-2 pt-3 border-t border-gray-200">
                                    <button onclick="updateReturnStatus('approved')"
                                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all text-sm">
                                        Approve Return
                                    </button>
                                    <button onclick="updateReturnStatus('rejected')"
                                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                                        Reject Return
                                    </button>
                                    @if ($order->return_status === 'approved')
                                        <button onclick="updateReturnStatus('completed')"
                                            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all text-sm">
                                            Complete Return
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Customer Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Customer</h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Name:</p>
                            <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Email:</p>
                            <p class="font-semibold text-gray-900">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Phone:</p>
                            <p class="font-semibold text-gray-900">{{ $order->customer_phone }}</p>
                        </div>
                        @if ($order->customer_address)
                            <div>
                                <p class="text-gray-600">Address:</p>
                                <p class="font-semibold text-gray-900">{{ $order->customer_address }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pricing Summary -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold text-gray-900">EGP
                                {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="flex items-center justify-between text-green-600">
                                <span>Discount:</span>
                                <span class="font-semibold">-EGP
                                    {{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        @if ($order->tax_amount > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-semibold text-gray-900">EGP
                                    {{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                        @endif
                        @if ($order->shipping_amount > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-semibold text-gray-900">EGP
                                    {{ number_format($order->shipping_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="pt-3 border-t border-gray-200 flex items-center justify-between">
                            <span class="font-bold text-gray-900">Total:</span>
                            <span class="text-2xl font-black text-red-600">EGP
                                {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-2">
                        @if ($order->status === 'pending' && $order->hasAllQRCodesAssigned() && $order->payment_status === 'paid')
                            <button onclick="changeStatus('processing')"
                                class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all text-sm">
                                Move to Processing
                            </button>
                        @endif

                        @if ($order->status === 'processing')
                            <button onclick="changeStatus('shipped')"
                                class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-all text-sm">
                                Mark as Shipped
                            </button>
                        @endif

                        @if ($order->status === 'shipped')
                            <button onclick="changeStatus('delivered')"
                                class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all text-sm">
                                Mark as Delivered
                            </button>
                        @endif

                        @if (in_array($order->status, ['pending', 'processing']))
                            <button onclick="changeStatus('cancelled')"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                                Cancel Order
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            async function changeStatus(newStatus) {
                const notes = prompt('Add notes (optional):');

                if (notes === null) return; // User cancelled

                try {
                    const response = await fetch('{{ route('orders.change-status', $order) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: newStatus,
                            notes: notes
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('Failed to update status');
                }
            }

            async function updateReturnStatus(newStatus) {
                const notes = prompt('Add notes (optional):');

                if (notes === null) return;

                try {
                    const response = await fetch('{{ route('orders.update-return-status', $order) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            return_status: newStatus,
                            notes: notes
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('Failed to update return status');
                }
            }
        </script>
    @endpush
</x-dashboard-layout>
