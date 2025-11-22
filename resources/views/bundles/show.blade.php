<x-dashboard-layout>
    <x-slot name="title">{{ $bundle->name }}</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('bundles.index') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Bundles
            </a>

            <div class="flex items-center gap-3">
                <form action="{{ route('bundles.duplicate', $bundle) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Duplicate
                    </button>
                </form>

                <a href="{{ route('bundles.edit', $bundle) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>

                <form action="{{ route('bundles.destroy', $bundle) }}" method="POST" class="inline"
                    onsubmit="return confirm('Are you sure you want to delete this bundle?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-br from-red-500 to-red-600 p-8 text-white">
                <div class="flex items-start justify-between">
                    <div>
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-full mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Bundle Package
                        </span>
                        <h1 class="text-3xl font-black mb-2">{{ $bundle->name }}</h1>
                        <p class="text-white/80">SKU: {{ $bundle->slug }}</p>
                    </div>

                    @if ($bundle->status === 'active')
                        <span
                            class="px-3 py-1.5 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-lg">Active</span>
                    @elseif($bundle->status === 'inactive')
                        <span
                            class="px-3 py-1.5 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-lg">Inactive</span>
                    @else
                        <span
                            class="px-3 py-1.5 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-lg">Archived</span>
                    @endif
                </div>

                @if ($bundle->description)
                    <p class="text-white/90 leading-relaxed mt-4">{{ $bundle->description }}</p>
                @endif
            </div>

            <!-- Pricing & Stock Section -->
            <div class="p-8 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Pricing & Stock Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-sm text-blue-700 mb-1">Total Value</p>
                        <p class="text-2xl font-black text-blue-900">EGP{{ number_format($bundle->total_price, 2) }}</p>
                        <p class="text-xs text-blue-600 mt-1">{{ $bundle->products->count() }} Products</p>
                    </div>

                    @if ($bundle->hasDiscount())
                        <div class="bg-red-50 rounded-xl p-4">
                            <p class="text-sm text-red-700 mb-1">Discount</p>
                            <p class="text-2xl font-black text-red-900">
                                @if ($bundle->discount_type === 'percentage')
                                    {{ $bundle->discount_amount }}%
                                @else
                                    EGP{{ number_format($bundle->discount_amount, 2) }}
                                @endif
                            </p>
                            <p class="text-xs text-red-600 mt-1">{{ ucfirst($bundle->discount_type) }}</p>
                        </div>

                        <div class="bg-yellow-50 rounded-xl p-4">
                            <p class="text-sm text-yellow-700 mb-1">You Save</p>
                            <p class="text-2xl font-black text-yellow-900">
                                EGP{{ number_format($bundle->total_price - $bundle->final_price, 2) }}</p>
                            <p class="text-xs text-yellow-600 mt-1">{{ number_format($bundle->discount_percentage) }}%
                                OFF</p>
                        </div>
                    @endif

                    <div class="bg-green-50 rounded-xl p-4">
                        <p class="text-sm text-green-700 mb-1">Bundle Price</p>
                        <p class="text-3xl font-black text-green-900">EGP{{ number_format($bundle->final_price, 2) }}
                        </p>
                        <p class="text-xs text-green-600 mt-1">Final Price</p>
                    </div>

                    <div class="bg-purple-50 rounded-xl p-4">
                        <p class="text-sm text-purple-700 mb-1">Stock Status</p>
                        <p class="text-2xl font-black text-purple-900">{{ $bundle->actual_stock_quantity }}</p>
                        <p class="text-xs text-purple-600 mt-1">
                            Set: {{ $bundle->stock_quantity }} |
                            @if ($bundle->stock_status === 'in_stock')
                                <span class="text-green-600 font-semibold">In Stock</span>
                            @elseif($bundle->stock_status === 'low_stock')
                                <span class="text-yellow-600 font-semibold">Low Stock</span>
                            @else
                                <span class="text-red-600 font-semibold">Out of Stock</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Products in Bundle -->
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Included Products ({{ $bundle->products->count() }})
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($bundle->products as $product)
                        <a href="{{ route('products.show', $product) }}"
                            class="group bg-gray-50 rounded-xl border-2 border-gray-200 overflow-hidden hover:border-red-500 hover:shadow-lg transition-all">
                            <div class="aspect-square bg-gray-100 relative">
                                @if ($product->image_path)
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <span
                                    class="inline-block px-2 py-0.5 bg-gray-200 text-gray-700 text-xs font-medium rounded mb-2">{{ $product->type_label }}</span>
                                <h3 class="font-bold text-gray-900 mb-2 group-hover:text-red-600 transition-colors">
                                    {{ $product->name }}</h3>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-lg font-black text-red-600">EGP{{ number_format($product->final_price, 2) }}</span>
                                    @if ($product->stock_status === 'in_stock')
                                        <span
                                            class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded">In
                                            Stock</span>
                                    @elseif($product->stock_status === 'low_stock')
                                        <span
                                            class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">Low
                                            Stock</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 bg-red-100 text-red-800 text-xs font-semibold rounded">Out
                                            of Stock</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- SEO Info -->
            @if ($bundle->meta_title || $bundle->meta_description)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">SEO Information</h2>

                    @if ($bundle->meta_title)
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Meta Title</p>
                            <p class="text-gray-600">{{ $bundle->meta_title }}</p>
                        </div>
                    @endif

                    @if ($bundle->meta_description)
                        <div>
                            <p class="text-sm font-semibold text-gray-700 mb-1">Meta Description</p>
                            <p class="text-gray-600">{{ $bundle->meta_description }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Timestamps -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">History</h2>

                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-600 mb-1">Created At</p>
                        <p class="font-semibold text-gray-900">{{ $bundle->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1">Last Updated</p>
                        <p class="font-semibold text-gray-900">{{ $bundle->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Bundle Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">Bundle Settings</h2>

                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <p class="text-gray-600">Featured</p>
                        <span
                            class="px-3 py-1 {{ $bundle->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }} text-xs font-semibold rounded-full">
                            {{ $bundle->is_featured ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-gray-600">Low Stock Alert</p>
                        <p class="font-semibold text-gray-900">{{ $bundle->stock_quantity_alert }} units</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bundle Summary Stats -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Bundle Statistics</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <p class="text-3xl font-black text-gray-900">{{ $bundle->products->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Total Products</p>
                </div>

                <div class="text-center">
                    <p class="text-3xl font-black text-green-600">
                        EGP{{ number_format($bundle->total_price - $bundle->final_price, 2) }}</p>
                    <p class="text-sm text-gray-600 mt-1">Total Savings</p>
                </div>

                <div class="text-center">
                    <p class="text-3xl font-black text-blue-600">{{ number_format($bundle->discount_percentage) }}%
                    </p>
                    <p class="text-sm text-gray-600 mt-1">Discount Rate</p>
                </div>

                <div class="text-center">
                    @php
                        $avgPrice =
                            $bundle->products->count() > 0 ? $bundle->final_price / $bundle->products->count() : 0;
                    @endphp
                    <p class="text-3xl font-black text-purple-600">EGP{{ number_format($avgPrice, 2) }}</p>
                    <p class="text-sm text-gray-600 mt-1">Avg. per Product</p>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
