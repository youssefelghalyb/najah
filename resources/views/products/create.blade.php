<x-dashboard-layout>
    <x-slot name="title">Create Product</x-slot>

    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Products
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gradient-to-r from-red-50 to-transparent p-6">
                <h2 class="text-2xl font-bold text-gray-900">Create New Product</h2>
                <p class="text-sm text-gray-600 mt-1">Add a new product to your inventory</p>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Basic Information</h3>

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">Slug <span class="text-gray-400 font-normal">(Auto-generated)</span></label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="auto-generated-from-name" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('slug') border-red-500 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Product Type *</label>
                            <select name="type" id="type" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('type') border-red-500 @enderror">
                                <option value="">Select Type</option>
                                <option value="card" {{ old('type') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="car_sticker" {{ old('type') == 'car_sticker' ? 'selected' : '' }}>Car Sticker</option>
                                <option value="bike_sticker" {{ old('type') == 'bike_sticker' ? 'selected' : '' }}>Bike Sticker</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" placeholder="Product description..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <span class="absolute left-4 top-1/2 -translate-y-1/2  text-gray-500 font-semibold">EGP</span>
                                <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required class=" w-full pl-12 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('price') border-red-500 @enderror" onchange="calculateFinalPrice()">
                            </div>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Discount -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="discount_amount" class="block text-sm font-semibold text-gray-700 mb-2">Discount</label>
                                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateFinalPrice()">
                            </div>
                            <div>
                                <label for="discount_type" class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                                <select name="discount_type" id="discount_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500" onchange="calculateFinalPrice()">
                                    <option value="fixed" {{ old('discount_type', 'fixed') == 'fixed' ? 'selected' : '' }}>Fixed (EGP)</option>
                                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Final Price Display -->
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-green-900">Final Price:</span>
                                <span class="text-2xl font-black text-green-600" id="finalPriceDisplay">EGP0.00</span>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">Stock Quantity *</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 @error('stock_quantity') border-red-500 @enderror">
                                @error('stock_quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="low_stock_threshold" class="block text-sm font-semibold text-gray-700 mb-2">Low Stock Alert</label>
                                <input type="number" name="low_stock_threshold" id="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                        </div>

                        <!-- Main Image -->
                        <div>
                            <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Product Image</label>
                            <label for="image" class="flex flex-col items-center justify-center px-4 py-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-sm text-gray-600 font-medium">Click to upload image</span>
                                <span class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP up to 2MB</span>
                                <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(this, 'imagePreview')">
                            </label>
                            <div id="imagePreview" class="hidden mt-3">
                                <img src="" alt="Preview" class="w-full h-48 object-cover border border-gray-200 rounded-lg">
                            </div>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gallery Images -->
                        <div>
                            <label for="gallery" class="block text-sm font-semibold text-gray-700 mb-2">Gallery Images</label>
                            <input type="file" name="gallery[]" id="gallery" accept="image/*" multiple class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                            <p class="mt-1 text-xs text-gray-500">You can select multiple images</p>
                        </div>
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">SEO Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" placeholder="SEO title for search engines" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3" placeholder="SEO description..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-gray-200">
                    <a href="{{ route('products.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function(e) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });

        // Calculate final price
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
            document.getElementById('finalPriceDisplay').textContent = 'EGP' + finalPrice.toFixed(2);
        }

        // Preview image
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

        // Initial calculation
        calculateFinalPrice();
    </script>
    @endpush
</x-dashboard-layout>