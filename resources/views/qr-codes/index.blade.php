<x-dashboard-layout>
    <x-slot name="title">QR Codes</x-slot>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">QR Codes</h2>
                <p class="text-sm text-gray-600 mt-1">Manage and generate QR codes</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('qr-codes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New QR Code
                </a>
                <a href="{{ route('qr-codes.bulk-create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-all shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bulk Generate
                </a>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('qr-codes.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, code, or UUID..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <button type="submit" class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-all">
                    Search
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('qr-codes.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="hidden bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-red-900">
                    <span id="selectedCount">0</span> items selected
                </span>
                <div class="flex items-center gap-3">
                    <button onclick="exportSelected()" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export
                    </button>
                    <button onclick="deleteSelected()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- QR Codes Grid -->
        @if($qrCodes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($qrCodes as $qrCode)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        <!-- Selection Checkbox -->
                        <div class="absolute top-4 left-4 z-10">
                            <input type="checkbox" value="{{ $qrCode->id }}" class="qr-checkbox w-5 h-5 text-red-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-red-500" onchange="updateBulkActions()">
                        </div>

                        <!-- QR Image -->
                        <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 p-8">
                            @if($qrCode->qr_image_path)
                                <img src="{{ Storage::url($qrCode->qr_image_path) }}" alt="QR Code" class="w-full aspect-square object-contain">
                            @else
                                <div class="w-full aspect-square flex items-center justify-center bg-gray-200 rounded-xl">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                @if($qrCode->status === 'active')
                                    <span class="px-2.5 py-1 bg-green-500 text-white text-xs font-bold rounded-lg shadow-lg">Active</span>
                                @elseif($qrCode->status === 'inactive')
                                    <span class="px-2.5 py-1 bg-gray-500 text-white text-xs font-bold rounded-lg shadow-lg">Inactive</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-500 text-white text-xs font-bold rounded-lg shadow-lg">Expired</span>
                                @endif
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">
                                {{ $qrCode->title ?? 'QR Code #' . $qrCode->code }}
                            </h3>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Code:</span>
                                    <span class="font-mono font-bold text-red-600">{{ $qrCode->code }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Scans:</span>
                                    <span class="font-semibold text-gray-900">{{ $qrCode->scan_count }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('qr-codes.show', $qrCode) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('qr-codes.download', $qrCode) }}" class="inline-flex items-center justify-center p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $qrCodes->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No QR codes found</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first QR code</p>
                <a href="{{ route('qr-codes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create QR Code
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.qr-checkbox:checked');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCount = document.getElementById('selectedCount');
            
            selectedCount.textContent = checkboxes.length;
            
            if (checkboxes.length > 0) {
                bulkActionsBar.classList.remove('hidden');
            } else {
                bulkActionsBar.classList.add('hidden');
            }
        }

        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('.qr-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        async function exportSelected() {
            const ids = getSelectedIds();
            
            if (ids.length === 0) {
                alert('Please select QR codes to export');
                return;
            }

            try {
                const response = await fetch('{{ route("qr-codes.export") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ qr_codes: ids })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.download_url;
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to export QR codes');
            }
        }

        async function deleteSelected() {
            const ids = getSelectedIds();
            
            if (ids.length === 0) {
                alert('Please select QR codes to delete');
                return;
            }

            if (!confirm(`Are you sure you want to delete ${ids.length} QR code(s)?`)) {
                return;
            }

            try {
                const response = await fetch('{{ route("qr-codes.bulk-destroy") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ qr_codes: ids })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Failed to delete QR codes');
            }
        }
    </script>
    @endpush
</x-dashboard-layout>