<x-dashboard-layout>
    <x-slot name="title">Edit Product</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Product
            </a>

            <form action="{{ route('products.duplicate', $product) }}" method="POST" class="inline">
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
                <h2 class="text-2xl font-bold text-gray-900">Edit Product</h2>
                <p class="text-sm text-gray-600 mt-1">Update product information</p>
            </div>

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Basic Information</h3>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Product Type *</label>
                            <select name="type" id="type" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="card" {{ old('type', $product->type) == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="car_sticker" {{ old('type', $product->type) == 'car_sticker' ? 'selected' : '' }}>Car Sticker</option>
                                <option value="bike_sticker" {{ old('type', $product->type) == 'bike_sticker' ? 'selected' : '' }}>Bike Sticker</option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Pricing & Stock</h3>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Price *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">$</span>
                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateFinalPrice()">
                            </div>
                        </div>

                        <!-- Discount -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="discount_amount" class="block text-sm font-semibold text-gray-700 mb-2">Discount</label>
                                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $product->discount_amount) }}" step="0.01" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateFinalPrice()">
                            </div>
                            <div>
                                <label for="discount_type" class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                                <select name="discount_type" id="discount_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateFinalPrice()">
                                    <option value="fixed" {{ old('discount_type', $product->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed ($)</option>
                                    <option value="percentage" {{ old('discount_type', $product->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Final Price Display -->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-green-900">Final Price:</span>
                                <span class="text-2xl font-black text-green-600" id="finalPriceDisplay">${{ number_format($product->final_price, 2) }}</span>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Stock Quantity *</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                            <div>
                                <label for="low_stock_threshold" class="block text-sm font-semibold text-gray-700 mb-2">Low Stock Alert</label>
                                <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                        </div>

                        <!-- Current Image -->
                        @if($product->image_path)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Image</label>
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover border border-gray-200 rounded-lg">
                            </div>
                        @endif

                        <!-- New Image -->
                        <div>
                            <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">{{ $product->image_path ? 'Replace Image' : 'Product Image' }}</label>
                            <input type="file" name="image" id="image" accept="image/*" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="previewImage(this, 'imagePreview')">
                            <div id="imagePreview" class="hidden mt-3">
                                <img src="" alt="Preview" class="w-full h-48 object-cover border border-gray-200 rounded-lg">
                            </div>
                        </div>

                        <!-- Gallery Images -->
                        @if($product->gallery_images && count($product->gallery_images) > 0)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Gallery Images</label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($product->gallery_images as $imagePath)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($imagePath) }}" alt="Gallery" class="w-full h-24 object-cover border border-gray-200 rounded-lg">
                                            <button type="button" onclick="deleteGalleryImage('{{ $imagePath }}')" class="absolute top-1 right-1 p-1 bg-red-600 hover:bg-red-700 text-white rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Add More Gallery Images -->
                        <div>
                            <label for="gallery" class="block text-sm font-semibold text-gray-700 mb-2">Add Gallery Images</label>
                            <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">SEO Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('meta_description', $product->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-gray-200">
                    <a href="{{ route('products.show', $product) }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function calculateFinalPrice() {
            const price = parseFloat(document.getElementById('price').value) || 0;
            const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
            const type = document.getElementById('discount_type').value;
            
            let finalPrice = price;
            
            if (type === 'percentage') {
                finalPrice = price - (price * (discount / 100));
            } else {
                finalPrice = price - discount;
            }
            
            finalPrice = Math.max(0, finalPrice);
            document.getElementById('finalPriceDisplay').textContent = '$' + finalPrice.toFixed(2);
        }

        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function deleteGalleryImage(imagePath) {
            if (!confirm('Delete this image?')) return;

            try {
                const response = await fetch('{{ route("products.delete-gallery-image", $product) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ image_path: imagePath })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to delete image');
            }
        }
    </script>
    @endpush
</x-dashboard-layout>