<x-dashboard-layout>
    <x-slot name="title">Bundles</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Product Bundles</h2>
                <p class="text-sm text-gray-600 mt-1">Manage your product bundles and packages</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('bundles.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Bundle
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search bundles..."
                    class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">

                <select name="status"
                    class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="submit"
                    class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-all">Search</button>
            </form>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="hidden bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-red-900">
                    <span id="selectedCount">0</span> bundles selected
                </span>
                <div class="flex items-center gap-3">
                    <button onclick="bulkUpdateStatus('active')"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all text-sm">
                        Activate
                    </button>
                    <button onclick="bulkUpdateStatus('inactive')"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-all text-sm">
                        Deactivate
                    </button>
                    <button onclick="bulkDelete()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all text-sm">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Bundles Grid -->
        @if ($bundles->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($bundles as $bundle)
                    <div
                        class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all">
                        <!-- Checkbox -->
                        <div class="absolute top-4 left-4 z-10">
                            <input type="checkbox" value="{{ $bundle->id }}"
                                class="bundle-checkbox w-5 h-5 text-red-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-red-500"
                                onchange="updateBulkActions()">
                        </div>

                        <!-- Header -->
                        <div class="relative bg-gradient-to-br from-red-500 to-red-600 p-6">
                            <div class="absolute top-4 right-4">
                                @if ($bundle->status === 'active')
                                    <span
                                        class="px-2.5 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-lg">Active</span>
                                @else
                                    <span
                                        class="px-2.5 py-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-lg">Inactive</span>
                                @endif
                            </div>

                            <div class="text-white">
                                <h3 class="text-xl font-black mb-2">{{ $bundle->name }}</h3>
                                <p class="text-sm text-white/80">{{ $bundle->products_count }} Products</p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            @if ($bundle->description)
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $bundle->description }}</p>
                            @endif

                            <!-- Products Preview -->
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Included Products</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($bundle->products->take(3) as $product)
                                        <span
                                            class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg">
                                            {{ $product->name }}
                                        </span>
                                    @endforeach
                                    @if ($bundle->products_count > 3)
                                        <span
                                            class="px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-medium rounded-lg">
                                            +{{ $bundle->products_count - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Pricing -->
                            <!-- Replace the pricing section with this enhanced version -->
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-gray-500">Total Value:</span>
                                    <span
                                        class="text-sm font-semibold text-gray-400 line-through">EGP{{ number_format($bundle->total_price, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-bold text-gray-900">Bundle Price:</span>
                                    <span
                                        class="text-2xl font-black text-red-600">EGP{{ number_format($bundle->final_price, 2) }}</span>
                                </div>
                                @if ($bundle->hasDiscount())
                                    <div class="flex items-center justify-end mb-2">
                                        <span class="text-xs font-bold text-green-600">Save
                                            {{ number_format($bundle->discount_percentage) }}%</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                    <span class="text-xs text-gray-500">Stock:</span>
                                    <span
                                        class="text-sm font-bold {{ $bundle->stock_status === 'in_stock' ? 'text-green-600' : ($bundle->stock_status === 'low_stock' ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $bundle->actual_stock_quantity }} units
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('bundles.show', $bundle) }}"
                                    class="flex-1 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-center transition-all text-sm">
                                    View
                                </a>
                                <a href="{{ route('bundles.edit', $bundle) }}"
                                    class="flex-1 px-3 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-center transition-all text-sm">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $bundles->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No bundles found</h3>
                <p class="text-gray-600 mb-6">Create your first bundle to get started</p>
                <a href="{{ route('bundles.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Bundle
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function updateBulkActions() {
                const checkboxes = document.querySelectorAll('.bundle-checkbox:checked');
                const bulkActionsBar = document.getElementById('bulkActionsBar');
                const selectedCount = document.getElementById('selectedCount');

                selectedCount.textContent = checkboxes.length;
                bulkActionsBar.classList.toggle('hidden', checkboxes.length === 0);
            }

            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.bundle-checkbox:checked')).map(cb => cb.value);
            }

            async function bulkUpdateStatus(status) {
                const ids = getSelectedIds();
                if (ids.length === 0) return;

                if (!confirm(`Are you sure you want to ${status} ${ids.length} bundle(s)?`)) return;

                try {
                    const response = await fetch('{{ route('bundles.bulk-update-status') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            bundles: ids,
                            status: status
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('Failed to update bundles');
                }
            }

            async function bulkDelete() {
                const ids = getSelectedIds();
                if (ids.length === 0) return;

                if (!confirm(`Are you sure you want to delete ${ids.length} bundle(s)?`)) return;

                try {
                    const response = await fetch('{{ route('bundles.bulk-delete') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            bundles: ids
                        })
                    });

                    const data = await response.json();
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('Failed to delete bundles');
                }
            }
        </script>
    @endpush
</x-dashboard-layout>
