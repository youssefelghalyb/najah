<x-dashboard-layout>
    <x-slot name="title">Create QR Code</x-slot>

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
            <div class="border-b border-gray-200 bg-gradient-to-r from-red-50 to-transparent p-6">
                <h2 class="text-2xl font-bold text-gray-900">Create New QR Code</h2>
                <p class="text-sm text-gray-600 mt-1">Generate a customized QR code with your design preferences</p>
            </div>

            <form action="{{ route('qr-codes.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column - Basic Info -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Basic Information</h3>

                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="e.g., Product QR, Event Entry" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea name="description" id="description" rows="3" placeholder="Add notes or details about this QR code..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiration Date -->
                        <div>
                            <label for="expires_at" class="block text-sm font-semibold text-gray-700 mb-2">Expiration Date <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent @error('expires_at') border-red-500 @enderror">
                            @error('expires_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Leave empty for no expiration</p>
                        </div>
                    </div>

                    <!-- Right Column - Design Customization -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 pb-2 border-b border-gray-200">Design Customization</h3>

                        <!-- Logo Upload -->
                        <div>
                            <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Logo <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <div class="flex items-center gap-4">
                                <label for="logo" class="flex-1 flex flex-col items-center justify-center px-4 py-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span class="text-sm text-gray-600 font-medium">Click to upload logo</span>
                                    <span class="text-xs text-gray-500 mt-1">PNG, JPG, SVG up to 2MB</span>
                                    <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="previewLogo(this)">
                                </label>
                            </div>
                            <div id="logoPreview" class="hidden mt-3">
                                <img src="" alt="Logo preview" class="w-24 h-24 object-contain border border-gray-200 rounded-lg">
                            </div>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color Settings -->
                        <div class="grid grid-cols-2 gap-4">
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
                        </div>

                        <!-- Style -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">QR Code Style</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 transition-colors">
                                    <input type="radio" name="style" value="square" {{ old('style', 'square') == 'square' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-12 h-12 mb-2 grid grid-cols-3 gap-1">
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                        <div class="bg-gray-900 rounded-sm"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Square</span>
                                    <div class="absolute inset-0 border-2 border-red-500 rounded-xl opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                </label>
                                <label class="relative flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 transition-colors">
                                    <input type="radio" name="style" value="dot" {{ old('style') == 'dot' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-12 h-12 mb-2 grid grid-cols-3 gap-1">
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                        <div class="bg-gray-900 rounded-full"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Dot</span>
                                    <div class="absolute inset-0 border-2 border-red-500 rounded-xl opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                </label>
                                <label class="relative flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 transition-colors">
                                    <input type="radio" name="style" value="rounded" {{ old('style') == 'rounded' ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-12 h-12 mb-2 grid grid-cols-3 gap-1">
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                        <div class="bg-gray-900 rounded"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Rounded</span>
                                    <div class="absolute inset-0 border-2 border-red-500 rounded-xl opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Size & Error Correction -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="size" class="block text-sm font-semibold text-gray-700 mb-2">Size (px)</label>
                                <input type="number" name="size" id="size" value="{{ old('size', 300) }}" min="100" max="1000" step="50" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="error_correction" class="block text-sm font-semibold text-gray-700 mb-2">Error Correction</label>
                                <select name="error_correction" id="error_correction" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="L" {{ old('error_correction') == 'L' ? 'selected' : '' }}>Low (7%)</option>
                                    <option value="M" {{ old('error_correction', 'M') == 'M' ? 'selected' : '' }}>Medium (15%)</option>
                                    <option value="Q" {{ old('error_correction') == 'Q' ? 'selected' : '' }}>Quartile (25%)</option>
                                    <option value="H" {{ old('error_correction') == 'H' ? 'selected' : '' }}>High (30%)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-gray-200">
                    <a href="{{ route('qr-codes.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Generate QR Code
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
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