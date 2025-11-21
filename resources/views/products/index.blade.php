<x-dashboard-layout>
    <x-slot name="title">Products</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Products</h2>
                <p class="text-sm text-gray-600 mt-1">Manage your printing products</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Product
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                
                <select name="type" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">All Types</option>
                    <option value="card" {{ request('type') == 'card' ? 'selected' : '' }}>Cards</option>
                    <option value="car_sticker" {{ request('type') == 'car_sticker' ? 'selected' : '' }}>Car Stickers</option>
                    <option value="bike_sticker" {{ request('type') == 'bike_sticker' ? 'selected' : '' }}>Bike Stickers</option>
                </select>
                
                <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                
                <select name="stock_status" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">All Stock</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
                
                <button type="submit" class="md:col-span-4 px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-all">Search</button>
            </form>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="hidden bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-red-900">
                    <span id="selectedCount">0</span> products selected
                </span>
                <div class="flex items-center gap-3">
                    <button onclick="bulkUpdateStatus('active')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all text-sm">
                        Activate
                    </button>
                    <button onclick="bulkUpdateStatus('inactive')" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all text-sm">
                        Deactivate
                    </button>
                    <button onclick="bulkDelete()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all group">
                        <!-- Checkbox -->
                        <div class="absolute top-4 left-4 z-10">
                            <input type="checkbox" value="{{ $product->id }}" class="product-checkbox w-5 h-5 text-red-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-red-500" onchange="updateBulkActions()">
                        </div>

                        <!-- Image -->
                        <div class="relative bg-gray-100 aspect-square">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                @if($product->status === 'active')
                                    <span class="px-2.5 py-1 bg-green-500 text-white text-xs font-bold rounded-lg">Active</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-500 text-white text-xs font-bold rounded-lg">Inactive</span>
                                @endif
                            </div>

                            <!-- Stock Badge -->
                            <div class="absolute bottom-4 right-4">
                                @if($product->stock_status === 'in_stock')
                                    <span class="px-2.5 py-1 bg-blue-500 text-white text-xs font-bold rounded-lg">In Stock</span>
                                @elseif($product->stock_status === 'low_stock')
                                    <span class="px-2.5 py-1 bg-yellow-500 text-white text-xs font-bold rounded-lg">Low Stock</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">Out of Stock</span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="mb-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase">{{ $product->type_label }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">{{ $product->name }}</h3>
                            
                            <div class="flex items-baseline gap-2 mb-4">
                                <span class="text-2xl font-black text-red-600">${{ number_format($product->final_price, 2) }}</span>
                                @if($product->hasDiscount())
                                    <span class="text-sm text-gray-400 line-through">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-xs font-bold text-green-600">-{{ number_format($product->discount_percentage) }}%</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-sm mb-4">
                                <span class="text-gray-600">Stock:</span>
                                <span class="font-bold text-gray-900">{{ $product->stock_quantity }}</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('products.show', $product) }}" class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-center transition-all text-sm">
                                    View
                                </a>
                                <a href="{{ route('products.edit', $product) }}" class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-center transition-all text-sm">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 mb-6">Create your first product to get started</p>
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Product
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.product-checkbox:checked');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = checkboxes.length;
            bulkActionsBar.classList.toggle('hidden', checkboxes.length === 0);
        }

        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
        }

        async function bulkUpdateStatus(status) {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (!confirm(`Are you sure you want to ${status} ${ids.length} product(s)?`)) return;

            try {
                const response = await fetch('{{ route("products.bulk-update-status") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ products: ids, status: status })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to update products');
            }
        }

        async function bulkDelete() {
            const ids = getSelectedIds();
            if (ids.length === 0) return;

            if (!confirm(`Are you sure you want to delete ${ids.length} product(s)?`)) return;

            try {
                const response = await fetch('{{ route("products.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ products: ids })
                });

                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to delete products');
            }
        }
    </script>
    @endpush
</x-dashboard-layout>