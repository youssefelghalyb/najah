<x-dashboard-layout>
    <x-slot name="title">Create Order</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('orders.index') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Orders
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gradient-to-r from-red-50 to-transparent p-6">
                <h2 class="text-2xl font-bold text-gray-900">Create New Order</h2>
                <p class="text-sm text-gray-600 mt-1">Add a new customer order</p>
            </div>

            <form action="{{ route('orders.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Customer Info -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Customer Information
                        </h3>

                        <!-- Customer Name -->
                        <div>
                            <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-2">Customer
                                Name *</label>
                            <input type="text" name="customer_name" id="customer_name"
                                value="{{ old('customer_name') }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('customer_name') border-red-500 @enderror">
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer Email -->
                        <div>
                            <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">Customer
                                Email</label>
                            <input type="email" name="customer_email" id="customer_email"
                                value="{{ old('customer_email') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('customer_email') border-red-500 @enderror">

                            @error('customer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer Phone -->
                        <div>
                            <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">Customer
                                Phone *</label>
                            <input type="tel" name="customer_phone" id="customer_phone"
                                value="{{ old('customer_phone') }}" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('customer_phone') border-red-500 @enderror">
                            @error('customer_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Customer Address -->
                        <div>
                            <label for="customer_address"
                                class="block text-sm font-semibold text-gray-700 mb-2">Customer Address</label>
                            <textarea name="customer_address" id="customer_address" rows="3" placeholder="Full address..."
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('customer_address') }}</textarea>
                        </div>

                        <!-- Customer Notes -->
                        <div>
                            <label for="customer_notes" class="block text-sm font-semibold text-gray-700 mb-2">Customer
                                Notes</label>
                            <textarea name="customer_notes" id="customer_notes" rows="3" placeholder="Notes from customer..."
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('customer_notes') }}</textarea>
                        </div>
                    </div>

                    <!-- Right Column - Payment & Notes -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Payment Information
                        </h3>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-semibold text-gray-700 mb-2">Payment
                                Status</label>
                            <select name="payment_status" id="payment_status"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="pending"
                                    {{ old('payment_status', 'pending') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid
                                </option>
                                <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed
                                </option>
                            </select>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">Payment
                                Method</label>
                            <select name="payment_method" id="payment_method"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Select Payment Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>
                                    Credit/Debit Card</option>
                                <option value="bank_transfer"
                                    {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                                <option value="mobile_wallet"
                                    {{ old('payment_method') == 'mobile_wallet' ? 'selected' : '' }}>Mobile Wallet
                                </option>
                            </select>
                        </div>

                        <!-- Additional Charges -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="discount_amount"
                                    class="block text-sm font-semibold text-gray-700 mb-2">Discount (EGP)</label>
                                <input type="number" name="discount_amount" id="discount_amount"
                                    value="{{ old('discount_amount', 0) }}" step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"
                                    onchange="calculateTotal()">
                            </div>
                            <div>
                                <label for="tax_amount" class="block text-sm font-semibold text-gray-700 mb-2">Tax
                                    (EGP)</label>
                                <input type="number" name="tax_amount" id="tax_amount"
                                    value="{{ old('tax_amount', 0) }}" step="0.01" min="0"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"
                                    onchange="calculateTotal()">
                            </div>
                        </div>

                        <div>
                            <label for="shipping_amount"
                                class="block text-sm font-semibold text-gray-700 mb-2">Shipping (EGP)</label>
                            <input type="number" name="shipping_amount" id="shipping_amount"
                                value="{{ old('shipping_amount', 0) }}" step="0.01" min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"
                                onchange="calculateTotal()">
                        </div>

                        <!-- Admin Notes -->
                        <div>
                            <label for="admin_notes" class="block text-sm font-semibold text-gray-700 mb-2">Admin
                                Notes</label>
                            <textarea name="admin_notes" id="admin_notes" rows="3" placeholder="Internal notes..."
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('admin_notes') }}</textarea>
                        </div>

                        <!-- Order Total Preview -->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-green-900">Subtotal:</span>
                                <span class="text-lg font-bold text-green-600" id="subtotalDisplay">EGP 0.00</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-green-200">
                                <span class="text-sm font-semibold text-green-900">Order Total:</span>
                                <span class="text-2xl font-black text-green-600" id="totalDisplay">EGP 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items Section -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Order Items *</h3>
                        <button type="button" onclick="addOrderItem()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Item
                        </button>
                    </div>

                    <div id="orderItemsContainer" class="space-y-4">
                        <!-- Order items will be added here dynamically -->
                    </div>

                    @error('items')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-gray-200">
                    <a href="{{ route('orders.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Available products and bundles data
            const products = @json($products);
            const bundles = @json($bundles);

            let itemCounter = 0;

            // Add order item
            function addOrderItem() {
                const container = document.getElementById('orderItemsContainer');
                const itemIndex = itemCounter++;

                const itemHtml = `
                <div class="order-item p-4 bg-gray-50 border border-gray-200 rounded-xl" data-index="${itemIndex}">
                    <div class="flex items-start justify-between mb-4">
                        <h4 class="font-semibold text-gray-900">Item #${itemIndex + 1}</h4>
                        <button type="button" onclick="removeOrderItem(${itemIndex})" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Item Type *</label>
                            <select name="items[${itemIndex}][item_type]" onchange="updateItemOptions(${itemIndex})" required class="item-type w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Select Type</option>
                                <option value="product">Product</option>
                                <option value="bundle">Bundle</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Item *</label>
                            <select name="items[${itemIndex}][item_id]" required class="item-select w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateTotal()">
                                <option value="">Select Item</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity *</label>
                            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" required class="item-quantity w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateTotal()">
                        </div>
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-600 item-price-display">
                        Select an item to see price
                    </div>
                </div>
            `;

                container.insertAdjacentHTML('beforeend', itemHtml);
            }

            // Remove order item
            function removeOrderItem(index) {
                const item = document.querySelector(`[data-index="${index}"]`);
                if (item) {
                    item.remove();
                    calculateTotal();
                }
            }

            // Update item options based on type
            function updateItemOptions(index) {
                const container = document.querySelector(`[data-index="${index}"]`);
                const typeSelect = container.querySelector('.item-type');
                const itemSelect = container.querySelector('.item-select');
                const priceDisplay = container.querySelector('.item-price-display');

                const type = typeSelect.value;
                itemSelect.innerHTML = '<option value="">Select Item</option>';
                priceDisplay.textContent = 'Select an item to see price';

                if (type === 'product') {
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = `${product.name} - EGP ${parseFloat(product.price).toFixed(2)}`;
                        option.dataset.price = product.price;
                        itemSelect.appendChild(option);
                    });
                } else if (type === 'bundle') {
                    bundles.forEach(bundle => {
                        const option = document.createElement('option');
                        option.value = bundle.id;
                        option.textContent = `${bundle.name} - EGP ${parseFloat(bundle.final_price).toFixed(2)}`;
                        option.dataset.price = bundle.final_price;
                        itemSelect.appendChild(option);
                    });
                }
            }

            // Calculate order total
            function calculateTotal() {
                let subtotal = 0;

                // Calculate subtotal from items
                document.querySelectorAll('.order-item').forEach(item => {
                    const itemSelect = item.querySelector('.item-select');
                    const quantity = parseInt(item.querySelector('.item-quantity').value) || 0;
                    const priceDisplay = item.querySelector('.item-price-display');

                    if (itemSelect.value) {
                        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                        const price = parseFloat(selectedOption.dataset.price) || 0;
                        const itemTotal = price * quantity;
                        subtotal += itemTotal;

                        priceDisplay.textContent =
                            `Unit Price: EGP ${price.toFixed(2)} × ${quantity} = EGP ${itemTotal.toFixed(2)}`;
                    } else {
                        priceDisplay.textContent = 'Select an item to see price';
                    }
                });

                const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
                const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
                const shipping = parseFloat(document.getElementById('shipping_amount').value) || 0;

                const total = subtotal - discount + tax + shipping;

                document.getElementById('subtotalDisplay').textContent = 'EGP ' + subtotal.toFixed(2);
                document.getElementById('totalDisplay').textContent = 'EGP ' + total.toFixed(2);
            }

            // Add first item on page load
            document.addEventListener('DOMContentLoaded', function() {
                addOrderItem();
            });
        </script>
    @endpush
</x-dashboard-layout>
