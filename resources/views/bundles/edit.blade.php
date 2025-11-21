<x-dashboard-layout>
    <x-slot name="title">Edit Bundle</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('bundles.show', $bundle) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Bundle
            </a>

            <form action="{{ route('bundles.duplicate', $bundle) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Duplicate
                </button>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gradient-to-r from-red-50 to-transparent p-6">
                <h2 class="text-2xl font-bold text-gray-900">Edit Bundle</h2>
                <p class="text-sm text-gray-600 mt-1">Update bundle information</p>
            </div>

            <form action="{{ route('bundles.update', $bundle) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Bundle Information</h3>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Bundle Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $bundle->name) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $bundle->slug) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description', $bundle->description) }}</textarea>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="active" {{ old('status', $bundle->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $bundle->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="archived" {{ old('status', $bundle->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Pricing</h3>

                        <!-- Total Price Display (Read-only) -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-blue-900">Total Products Value:</span>
                                <span class="text-2xl font-black text-blue-600" id="totalPriceDisplay">${{ number_format($bundle->total_price, 2) }}</span>
                            </div>
                            <p class="text-xs text-blue-700 mt-1">Sum of all selected products</p>
                        </div>

                        <!-- Discount -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="discount_amount" class="block text-sm font-semibold text-gray-700 mb-2">Bundle Discount</label>
                                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $bundle->discount_amount) }}" step="0.01" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateFinalPrice()">
                            </div>
                            <div>
                                <label for="discount_type" class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                                <select name="discount_type" id="discount_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateFinalPrice()">
                                    <option value="fixed" {{ old('discount_type', $bundle->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed ($)</option>
                                    <option value="percentage" {{ old('discount_type', $bundle->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Final Price Display -->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-green-900">Final Bundle Price:</span>
                                <span class="text-3xl font-black text-green-600" id="finalPriceDisplay">${{ number_format($bundle->final_price, 2) }}</span>
                            </div>
                            <p class="text-xs text-green-700 mt-1" id="savingsDisplay">
                                @if($bundle->hasDiscount())
                                    Save ${{ number_format($bundle->total_price - $bundle->final_price, 2) }} ({{ number_format($bundle->discount_percentage) }}%)
                                @else
                                    No discount applied
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Products Selection -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Bundle Products *</h3>
                        <span class="text-sm text-gray-500">Select at least 2 products</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($products as $product)
                            @php
                                $isSelected = $bundle->products->contains($product->id) || in_array($product->id, old('products', []));
                            @endphp
                            <label class="relative flex items-start p-4 bg-gray-50 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="checkbox" name="products[]" value="{{ $product->id }}" data-price="{{ $product->final_price }}" class="product-checkbox mt-1 w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-2 focus:ring-red-500" onchange="updatePricing()" {{ $isSelected ? 'checked' : '' }}>
                                
                                <div class="ml-3 flex-1">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                            <span class="inline-block px-2 py-0.5 bg-gray-200 text-gray-700 text-xs font-medium rounded mt-1">{{ $product->type_label }}</span>
                                        </div>
                                        @if($product->image_path)
                                            <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg ml-2">
                                        @endif
                                    </div>
                                    <p class="text-sm font-black text-red-600 mt-2">${{ number_format($product->final_price, 2) }}</p>
                                    
                                    @if($product->stock_status !== 'in_stock')
                                        <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded mt-1">{{ $product->stock_status === 'low_stock' ? 'Low Stock' : 'Out of Stock' }}</span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    
                    @error('products')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SEO Section -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">SEO Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $bundle->meta_title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('meta_description', $bundle->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-gray-200">
                    <a href="{{ route('bundles.show', $bundle) }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Bundle
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update pricing when products are selected
        function updatePricing() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            let totalPrice = 0;

            checkboxes.forEach(checkbox => {
                totalPrice += parseFloat(checkbox.dataset.price);
            });

            document.getElementById('totalPriceDisplay').textContent = '$' + totalPrice.toFixed(2);
            calculateFinalPrice();
        }

        // Calculate final price with discount
        function calculateFinalPrice() {
            const totalPrice = parseFloat(document.getElementById('totalPriceDisplay').textContent.replace('$', '')) || 0;
            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const type = document.getElementById('discount_type').value;
            
            let finalPrice = totalPrice;
            let savings = 0;
            
            if (type === 'percentage') {
                savings = totalPrice * (discount / 100);
                finalPrice = totalPrice - savings;
            } else {
                savings = discount;
                finalPrice = totalPrice - discount;
            }
            
            finalPrice = Math.max(0, finalPrice);
            
            document.getElementById('finalPriceDisplay').textContent = '$' + finalPrice.toFixed(2);
            
            if (savings > 0) {
                const savingsPercent = totalPrice > 0 ? (savings / totalPrice * 100).toFixed(0) : 0;
                document.getElementById('savingsDisplay').textContent = `Save $${savings.toFixed(2)} (${savingsPercent}%)`;
            } else {
                document.getElementById('savingsDisplay').textContent = 'No discount applied';
            }
        }

        // Initial calculation
        calculateFinalPrice();
    </script>
    @endpush
</x-dashboard-layout>