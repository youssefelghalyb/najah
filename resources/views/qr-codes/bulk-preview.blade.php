<x-dashboard-layout>
    <x-slot name="title">Bulk QR Codes Preview</x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('qr-codes.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to All QR Codes
            </a>

            <button onclick="exportAll()" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export All QR Codes
            </button>
        </div>

        <!-- Success Banner -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl shadow-lg p-8 mb-8 text-white">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0 w-16 h-16 bg-white rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-black mb-1">Successfully Generated!</h2>
                    <p class="text-green-100">{{ $qrCodes->count() }} QR codes have been created and are ready to use</p>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="w-5 h-5 text-red-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-red-500">
                        <span class="text-sm font-semibold text-gray-700">Select All</span>
                    </label>
                    <span class="text-sm text-gray-500" id="selectedCount">0 selected</span>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="exportSelected()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- QR Codes Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($qrCodes as $qrCode)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                    <!-- Selection Checkbox -->
                    <div class="absolute top-4 left-4 z-10">
                        <input type="checkbox" value="{{ $qrCode->id }}" class="qr-checkbox w-5 h-5 text-red-600 bg-white border-gray-300 rounded focus:ring-2 focus:ring-red-500" onchange="updateSelectedCount()">
                    </div>

                    <!-- QR Image -->
                    <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 p-8">
                        @if($qrCode->qr_image_path)
                            <img src="{{ Storage::url($qrCode->qr_image_path) }}" alt="QR Code" class="w-full aspect-square object-contain">
                        @endif
                        
                        <!-- Download Badge -->
                        <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('qr-codes.download', $qrCode) }}" class="flex items-center justify-center w-10 h-10 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-900 mb-3 truncate">
                            {{ $qrCode->title ?? 'QR Code #' . $qrCode->code }}
                        </h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Code:</span>
                                <span class="font-mono font-bold text-red-600">{{ $qrCode->code }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Created:</span>
                                <span class="font-semibold text-gray-900">{{ $qrCode->created_at->format('M d, Y') }}</span>
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

        <!-- Summary Card -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Batch Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <p class="text-3xl font-black text-red-600 mb-1">{{ $qrCodes->count() }}</p>
                    <p class="text-sm text-gray-600 font-medium">Total Generated</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-black text-gray-900 mb-1">{{ $qrCodes->where('status', 'active')->count() }}</p>
                    <p class="text-sm text-gray-600 font-medium">Active</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-black text-gray-900 mb-1">{{ $qrCodes->first()->size ?? 300 }}px</p>
                    <p class="text-sm text-gray-600 font-medium">Size</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-black text-gray-900 mb-1 capitalize">{{ $qrCodes->first()->style ?? 'Square' }}</p>
                    <p class="text-sm text-gray-600 font-medium">Style</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.qr-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.qr-checkbox:checked');
            const count = checkboxes.length;
            const total = document.querySelectorAll('.qr-checkbox').length;
            
            document.getElementById('selectedCount').textContent = `${count} selected`;
            document.getElementById('selectAll').checked = count === total;
        }

        function getSelectedIds() {
            const checkboxes = document.querySelectorAll('.qr-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function getAllIds() {
            const checkboxes = document.querySelectorAll('.qr-checkbox');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        async function exportSelected() {
            const ids = getSelectedIds();
            
            if (ids.length === 0) {
                alert('Please select QR codes to export');
                return;
            }

            await exportQrCodes(ids);
        }

        async function exportAll() {
            const ids = getAllIds();
            await exportQrCodes(ids);
        }

        async function exportQrCodes(ids) {
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
    </script>
    @endpush
</x-dashboard-layout>