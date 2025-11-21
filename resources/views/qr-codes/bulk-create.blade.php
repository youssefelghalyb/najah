<x-dashboard-layout>
    <x-slot name="title">Bulk Generate QR Codes</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('qr-codes.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to QR Codes
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gradient-to-r from-gray-900 to-gray-800 p-6">
                <h2 class="text-2xl font-bold text-white">Bulk Generate QR Codes</h2>
                <p class="text-sm text-gray-300 mt-1">Create multiple QR codes with the same design template</p>
            </div>

            <form action="{{ route('qr-codes.bulk-store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="space-y-8">
                    <!-- Quantity Section -->
                    <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl p-6 border border-red-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            Quantity
                        </h3>
                        <div>
                            <label for="count" class="block text-sm font-semibold text-gray-700 mb-2">Number of QR Codes</label>
                            <input type="number" name="count" id="count" value="{{ old('count', 10) }}" min="1" max="1000" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-lg font-bold @error('count') border-red-500 @enderror">
                            @error('count')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-600">Generate between 1 and 1,000 QR codes at once</p>
                        </div>
                    </div>

                    <!-- Template Information -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">Template Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Title Prefix</label>
                                <input type="text" name="title" id="title" value="{{ old('title', 'QR Code') }}" placeholder="e.g., Event Ticket, Product" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('title') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Each QR will be numbered automatically (e.g., "Event Ticket #1")</p>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-2">Expiration Date <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('expires_at') border-red-500 @enderror">
                                @error('expires_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea name="description" id="description" rows="2" placeholder="Shared description for all QR codes..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Design Template -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-200">Design Template</h3>
                        
                        <!-- Logo Upload -->
                        <div class="mb-6">
                            <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Logo <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <label for="logo" class="flex flex-col items-center justify-center px-4 py-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                                <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-sm text-gray-600 font-medium">Click to upload logo for all QR codes</span>
                                <span class="text-xs text-gray-500 mt-1">PNG, JPG, SVG up to 2MB</span>
                                <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="previewLogo(this)">
                            </label>
                            <div id="logoPreview" class="hidden mt-3">
                                <img src="" alt="Logo preview" class="w-24 h-24 object-contain border border-gray-200 rounded-lg mx-auto">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Colors -->
                            <div>
                                <label for="foreground_color" class="block text-sm font-semibold text-gray-700 mb-2">Foreground Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="foreground_color" id="foreground_color" value="{{ old('foreground_color', '#000000') }}" class="h-12 w-16 rounded-lg cursor-pointer border border-gray-200">
                                    <input type="text" id="foreground_color_text" value="{{ old('foreground_color', '#000000') }}" class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono" readonly>
                                </div>
                            </div>
                            <div>
                                <label for="background_color" class="block text-sm font-semibold text-gray-700 mb-2">Background Color</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="background_color" id="background_color" value="{{ old('background_color', '#ffffff') }}" class="h-12 w-16 rounded-lg cursor-pointer border border-gray-200">
                                    <input type="text" id="background_color_text" value="{{ old('background_color', '#ffffff') }}" class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono" readonly>
                                </div>
                            </div>

                            <!-- Style -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">QR Code Style</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="relative flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 transition-colors">
                                        <input type="radio" name="style" value="square" {{ old('style', 'square') == 'square' ? 'checked' : '' }} class="sr-only peer">
                                        <span class="text-xs font-medium text-gray-700">Square</span>
                                        <div class="absolute inset-0 border-2 border-red-500 rounded-lg opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                    </label>
                                    <label class="relative flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 transition-colors">
                                        <input type="radio" name="style" value="dot" {{ old('style') == 'dot' ? 'checked' : '' }} class="sr-only peer">
                                        <span class="text-xs font-medium text-gray-700">Dot</span>
                                        <div class="absolute inset-0 border-2 border-red-500 rounded-lg opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                    </label>
                                    <label class="relative flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 transition-colors">
                                        <input type="radio" name="style" value="rounded" {{ old('style') == 'rounded' ? 'checked' : '' }} class="sr-only peer">
                                        <span class="text-xs font-medium text-gray-700">Rounded</span>
                                        <div class="absolute inset-0 border-2 border-red-500 rounded-lg opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Size & Error Correction -->
                            <div class="space-y-4">
                                <div>
                                    <label for="size" class="block text-sm font-semibold text-gray-700 mb-2">Size (px)</label>
                                    <input type="number" name="size" id="size" value="{{ old('size', 300) }}" min="100" max="1000" step="50" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="error_correction" class="block text-sm font-semibold text-gray-700 mb-2">Error Correction</label>
                                    <select name="error_correction" id="error_correction" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                        <option value="L">Low (7%)</option>
                                        <option value="M" selected>Medium (15%)</option>
                                        <option value="Q">Quartile (25%)</option>
                                        <option value="H">High (30%)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold" id="estimatedCount">10</span> QR codes will be generated
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('qr-codes.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-all shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Generate QR Codes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Update estimated count
        document.getElementById('count').addEventListener('input', function(e) {
            document.getElementById('estimatedCount').textContent = e.target.value || 0;
        });

        // Sync color inputs
        document.getElementById('foreground_color').addEventListener('input', function(e) {
            document.getElementById('foreground_color_text').value = e.target.value;
        });

        document.getElementById('background_color').addEventListener('input', function(e) {
            document.getElementById('background_color_text').value = e.target.value;
        });

        // Logo preview
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logoPreview');
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-dashboard-layout>