<x-dashboard-layout>
    <x-slot name="title">Assign QR Codes - {{ $order->order_number }}</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors mb-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Order
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Assign QR Codes</h1>
                <p class="text-sm text-gray-600 mt-1">Order: {{ $order->order_number }}</p>
            </div>

            <button type="submit" form="assignQRForm" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Assignments
            </button>
        </div>

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Assign one QR code to each order item</p>
                    <p class="text-xs text-blue-700 mt-1">Select a QR code from the dropdown for each item. Only unassigned QR codes are shown.</p>
                </div>
            </div>
        </div>

        <form id="assignQRForm" action="{{ route('orders.assign-qr-codes.store', $order) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                @foreach($order->items as $index => $item)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 border-b border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $item->item_name }}</h3>
                                    <span class="inline-block px-2 py-0.5 bg-gray-200 text-gray-700 text-xs font-medium rounded mt-1">
                                        {{ $item->item_type_label }}
                                    </span>
                                </div>
                                <span class="text-lg font-black text-gray-900">EGP {{ number_format($item->total, 2) }}</span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Item Details -->
                                <div>
                                    <h4 class="text-sm font-bold text-gray-700 mb-3">Item Details</h4>
                                    @if($item->item_description)
                                        <p class="text-sm text-gray-600 mb-3">{{ $item->item_description }}</p>
                                    @endif
                                    <div class="space-y-2 text-sm">
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Quantity:</span>
                                            <span class="font-semibold text-gray-900">{{ $item->quantity }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Unit Price:</span>
                                            <span class="font-semibold text-gray-900">EGP {{ number_format($item->unit_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- QR Code Assignment -->
                                <div>
                                    <h4 class="text-sm font-bold text-gray-700 mb-3">QR Code Assignment</h4>
                                    
                                    @if($item->qrCode)
                                        <!-- Already Assigned -->
                                        <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                                            <div class="flex items-start justify-between mb-3">
                                                <div>
                                                    <p class="text-sm font-bold text-green-900 flex items-center gap-2">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        QR Code Assigned
                                                    </p>
                                                    <p class="text-sm text-green-800 mt-1">Code: <strong>{{ $item->qrCode->code }}</strong></p>
                                                    <p class="text-xs text-green-700 mt-1">Assigned: {{ $item->qr_assigned_at->format('M d, Y h:i A') }}</p>
                                                </div>
                                                <button type="button" onclick="unassignQRCode({{ $item->id }})" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-all">
                                                    Unassign
                                                </button>
                                            </div>
                                            <a href="{{ route('qr-codes.show', $item->qrCode) }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                View QR Code Details →
                                            </a>
                                        </div>
                                    @else
                                        <!-- Not Assigned - Show Dropdown -->
                                        <div>
                                            <label for="qr_code_{{ $item->id }}" class="block text-sm font-semibold text-gray-700 mb-2">Select QR Code *</label>
                                            <select name="qr_assignments[{{ $index }}][qr_code_id]" id="qr_code_{{ $item->id }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                                <option value="">-- Select QR Code --</option>
                                                @foreach($availableQRCodes as $qrCode)
                                                    <option value="{{ $qrCode->id }}">
                                                        {{ $qrCode->code }} - {{ $qrCode->title ?? 'Untitled' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="qr_assignments[{{ $index }}][order_item_id]" value="{{ $item->id }}">
                                            
                                            @if($availableQRCodes->isEmpty())
                                                <p class="mt-2 text-sm text-red-600 font-semibold">⚠️ No available QR codes. Please create QR codes first.</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($order->items->where('qr_code_id', null)->count() > 0)
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-yellow-900">
                        📋 You have {{ $order->items->where('qr_code_id', null)->count() }} item(s) without QR codes assigned.
                    </p>
                    <p class="text-xs text-yellow-700 mt-1">Assign QR codes to all items to move order to processing.</p>
                </div>
            @else
                <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-green-900 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        All items have QR codes assigned! You can now move the order to processing.
                    </p>
                </div>
            @endif
        </form>
    </div>

    @push('scripts')
    <script>
        async function unassignQRCode(orderItemId) {
            if (!confirm('Are you sure you want to unassign this QR code?')) return;

            try {
                const response = await fetch(`{{ route("orders.show", $order) }}/unassign-qr-code/${orderItemId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to unassign QR code');
            }
        }
    </script>
    @endpush
</x-dashboard-layout>