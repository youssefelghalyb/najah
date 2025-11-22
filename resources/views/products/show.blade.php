<x-dashboard-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Products
            </a>

            <div class="flex items-center gap-3">
                <form action="{{ route('products.duplicate', $product) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Duplicate
                    </button>
                </form>

                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>

                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Images -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Main Image -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="aspect-square bg-gray-100">
                        @if($product->image_path)
                            <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery -->
                @if($product->gallery_images && count($product->gallery_images) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">Gallery</h3>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($product->gallery_images as $imagePath)
                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:opacity-75 transition-opacity">
                                    <img src="{{ Storage::url($imagePath) }}" alt="Gallery" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Main Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full mb-3">
                                {{ $product->type_label }}
                            </span>
                            <h1 class="text-3xl font-black text-gray-900 mb-2">{{ $product->name }}</h1>
                            <p class="text-sm text-gray-500">SKU: {{ $product->slug }}</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            @if($product->status === 'active')
                                <span class="px-3 py-1.5 bg-green-100 text-green-800 text-xs font-bold rounded-lg">Active</span>
                            @elseif($product->status === 'inactive')
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-800 text-xs font-bold rounded-lg">Inactive</span>
                            @else
                                <span class="px-3 py-1.5 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-lg">Archived</span>
                            @endif
                        </div>
                    </div>

                    @if($product->description)
                        <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                    @endif
                </div>

                <!-- Pricing -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">Pricing</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Base Price</p>
                            <p class="text-2xl font-black text-gray-900">EGP{{ number_format($product->price, 2) }}</p>
                        </div>

                        @if($product->hasDiscount())
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Discount</p>
                                <p class="text-2xl font-black text-red-600">
                                    @if($product->discount_type === 'percentage')
                                        {{ $product->discount_amount }}%
                                    @else
                                        EGP{{ number_format($product->discount_amount, 2) }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        <div class="md:col-span-{{ $product->hasDiscount() ? '1' : '2' }}">
                            <p class="text-sm text-gray-600 mb-1">Final Price</p>
                            <p class="text-3xl font-black text-green-600">EGP{{ number_format($product->final_price, 2) }}</p>
                            @if($product->hasDiscount())
                                <p class="text-sm text-green-600 font-semibold mt-1">Save {{ number_format($product->discount_percentage) }}%</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Stock Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">Stock Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Current Stock</p>
                            <p class="text-3xl font-black text-gray-900">{{ $product->stock_quantity }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 mb-1">Low Stock Alert</p>
                            <p class="text-2xl font-black text-yellow-600">{{ $product->low_stock_threshold ?? 'Not Set' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 mb-1">Status</p>
                            @if($product->stock_status === 'in_stock')
                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-800 font-bold rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    In Stock
                                </span>
                            @elseif($product->stock_status === 'low_stock')
                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-100 text-yellow-800 font-bold rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-800 font-bold rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Out of Stock
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SEO Info -->
                @if($product->meta_title || $product->meta_description)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">SEO Information</h2>
                        
                        @if($product->meta_title)
                            <div class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-1">Meta Title</p>
                                <p class="text-gray-600">{{ $product->meta_title }}</p>
                            </div>
                        @endif

                        @if($product->meta_description)
                            <div>
                                <p class="text-sm font-semibold text-gray-700 mb-1">Meta Description</p>
                                <p class="text-gray-600">{{ $product->meta_description }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">History</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 mb-1">Created At</p>
                            <p class="font-semibold text-gray-900">{{ $product->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Last Updated</p>
                            <p class="font-semibold text-gray-900">{{ $product->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>