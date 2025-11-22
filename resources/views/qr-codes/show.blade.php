<x-dashboard-layout>
    <x-slot name="title">QR Code Details</x-slot>

    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('qr-codes.index') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to QR Codes
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- QR Code Display -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8">
                        @if ($qrCode->qr_image_path)
                            <img src="{{ Storage::url($qrCode->qr_image_path) }}" alt="QR Code"
                                class="w-full aspect-square object-contain">
                        @else
                            <div class="w-full aspect-square flex items-center justify-center bg-gray-200 rounded-xl">
                                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Download Button -->
                        <a href="{{ route('qr-codes.download', $qrCode) }}"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-500/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download QR Code
                        </a>


                        <!-- Toggle Status -->
                        <button onclick="toggleStatus()"
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span id="statusText">{{ $qrCode->status === 'active' ? 'Deactivate' : 'Activate' }}</span>
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('qr-codes.destroy', $qrCode) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this QR code?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-all border border-red-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete QR Code
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- QR Code Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $qrCode->title ?? 'QR Code #' . $qrCode->code }}
                        </h2>
                        @if ($qrCode->status === 'active')
                            <span
                                class="px-3 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-lg">Active</span>
                        @elseif($qrCode->status === 'inactive')
                            <span
                                class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-bold rounded-lg">Inactive</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-bold rounded-lg">Expired</span>
                        @endif
                    </div>

                    @if ($qrCode->description)
                        <p class="text-gray-600 mb-6">{{ $qrCode->description }}</p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-500 mb-1">Unique Code</label>
                            <div class="flex items-center gap-2">
                                <p class="text-2xl font-black text-red-600 font-mono">{{ $qrCode->code }}</p>
                                <button onclick="copyCode('{{ $qrCode->code }}')"
                                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-500 mb-1">Total Scans</label>
                            <p class="text-2xl font-black text-gray-900">{{ number_format($qrCode->scan_count) }}</p>
                        </div>
                    </div>
                </div>

                <!-- QR URL -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">QR Code URL</h3>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <code class="flex-1 text-sm text-gray-700 break-all">{{ $qrCode->full_url }}</code>
                        <button onclick="copyUrl('{{ $qrCode->full_url }}')"
                            class="flex-shrink-0 p-2 hover:bg-gray-200 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Design Settings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Design Settings</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Foreground</label>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg border border-gray-200"
                                    style="background-color: {{ $qrCode->foreground_color }}"></div>
                                <span class="text-xs font-mono text-gray-700">{{ $qrCode->foreground_color }}</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Background</label>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg border border-gray-200"
                                    style="background-color: {{ $qrCode->background_color }}"></div>
                                <span class="text-xs font-mono text-gray-700">{{ $qrCode->background_color }}</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Style</label>
                            <span class="text-sm font-bold text-gray-900 capitalize">{{ $qrCode->style }}</span>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-xs font-semibold text-gray-500 mb-2">Size</label>
                            <span class="text-sm font-bold text-gray-900">{{ $qrCode->size }}px</span>
                        </div>
                    </div>
                </div>

                <!-- Order Information -->
                @if ($qrCode->isAssigned())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Order Assignment</h3>
                            <span
                                class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-bold rounded-lg">Assigned</span>
                        </div>

                        @php
                            $orderItem = $qrCode->orderItem;
                            $order = $orderItem ? $orderItem->order : null;
                        @endphp

                        @if ($order)
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Order Number:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $order->order_number }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Customer:</span>
                                    <span
                                        class="text-sm font-semibold text-gray-900">{{ $order->customer_name }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Item:</span>
                                    <span
                                        class="text-sm font-semibold text-gray-900">{{ $orderItem->item_name }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 {{ $order->status_badge_class }} text-xs font-bold rounded-lg">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-sm text-gray-600">Assigned At:</span>
                                    <span
                                        class="text-sm font-semibold text-gray-900">{{ $orderItem->qr_assigned_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('orders.show', $order) }}"
                                    class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    View Full Order
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Not Assigned to Order</h3>
                            <p class="text-sm text-gray-600">This QR code is not assigned to any order yet</p>
                        </div>
                    </div>
                @endif

                <!-- Profile Information -->
                @if ($qrCode->hasProfile())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Linked Profile</h3>
                            <span
                                class="px-3 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-lg">Active</span>
                        </div>

                        @php $profile = $qrCode->getLinkedProfile(); @endphp

                        <div class="space-y-3">
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Name:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $profile->name }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Email:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $profile->email }}</span>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Age:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $profile->age }} years</span>
                            </div>
                            @if ($profile->blood_type)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Blood Type:</span>
                                    <span class="text-sm font-bold text-red-600">{{ $profile->blood_type }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between py-3">
                                <span class="text-sm text-gray-600">Linked At:</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ $qrCode->profiles()->first()->pivot->linked_at }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
                            <a href="{{ route('profiles.show', $profile) }}"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                View Full Profile
                            </a>
                            <button onclick="showUnlinkModal()"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-lg transition-all border border-red-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                Unlink Profile
                            </button>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No Profile Linked</h3>
                            <p class="text-sm text-gray-600 mb-4">This QR code doesn't have a profile yet</p>
                            @if ($qrCode->isAssigned())
                                <p class="text-xs text-gray-500 mb-4">Customer will create profile when they scan the
                                    QR code</p>
                            @endif

                            <!-- Link to Existing Profile -->
                            <button onclick="showLinkModal()"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                                Link to Existing Profile
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Link Profile Modal -->
                <div id="linkModal"
                    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-900">Link to Existing Profile</h3>
                            <button onclick="hideLinkModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('qr-codes.link-profile', $qrCode) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Profile QR Code
                                        *</label>
                                    <input type="text" name="profile_qr_code"
                                        placeholder="Enter 5-digit QR code of profile" maxlength="5"
                                        pattern="[0-9]{5}" required
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <p class="mt-1 text-xs text-gray-500">Enter the QR code that's already linked to
                                        the profile</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes
                                        (Optional)</label>
                                    <textarea name="notes" rows="3" placeholder="Add notes about this linkage..."
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                                </div>

                                <div class="flex gap-3 pt-4">
                                    <button type="button" onclick="hideLinkModal()"
                                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all">
                                        Link Profile
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Unlink Profile Modal -->
                <div id="unlinkModal"
                    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-900">Unlink Profile</h3>
                            <button onclick="hideUnlinkModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="text-gray-600 mb-6">Are you sure you want to unlink this profile? The profile will
                            still exist and can be linked to another QR code.</p>

                        <form action="{{ route('qr-codes.unlink-profile', $qrCode) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="flex gap-3">
                                <button type="button" onclick="hideUnlinkModal()"
                                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all">
                                    Unlink
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Created</span>
                            <span
                                class="text-sm font-semibold text-gray-900">{{ $qrCode->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if ($qrCode->last_scanned_at)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Last Scanned</span>
                                <span
                                    class="text-sm font-semibold text-gray-900">{{ $qrCode->last_scanned_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endif
                        @if ($qrCode->expires_at)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Expires</span>
                                <span
                                    class="text-sm font-semibold {{ $qrCode->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $qrCode->expires_at->format('M d, Y H:i') }}
                                </span>
                            </div>
                        @endif
                        @if ($qrCode->creator)
                            <div class="flex items-center justify-between py-3">
                                <span class="text-sm text-gray-600">Created By</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $qrCode->creator->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showLinkModal() {
                document.getElementById('linkModal').classList.remove('hidden');
            }

            function hideLinkModal() {
                document.getElementById('linkModal').classList.add('hidden');
            }

            function showUnlinkModal() {
                document.getElementById('unlinkModal').classList.remove('hidden');
            }

            function hideUnlinkModal() {
                document.getElementById('unlinkModal').classList.add('hidden');
            }

            function copyCode(code) {
                navigator.clipboard.writeText(code).then(() => {
                    alert('Code copied to clipboard!');
                });
            }

            function copyUrl(url) {
                navigator.clipboard.writeText(url).then(() => {
                    alert('URL copied to clipboard!');
                });
            }

            async function toggleStatus() {
                try {
                    const response = await fetch('{{ route('qr-codes.toggle-status', $qrCode) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to update status');
                    }
                } catch (error) {
                    alert('An error occurred');
                }
            }
        </script>
    @endpush
</x-dashboard-layout>
