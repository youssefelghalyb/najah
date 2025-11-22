<x-dashboard-layout>
    <x-slot name="title">Profile Details - {{ $profile->name }}</x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('profiles.index') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Profiles
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('profiles.manage-qr-codes', $profile) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Manage QR Codes
                </a>

                <a href="{{ route('profiles.edit', $profile) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profile
                </a>

                <form action="{{ route('profiles.destroy', $profile) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this profile?')">
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

        <!-- Profile Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-start gap-6">
                @if ($profile->profile_image)
                    <img src="{{ Storage::url($profile->profile_image) }}" alt="{{ $profile->name }}"
                        class="w-24 h-24 rounded-full object-cover">
                @else
                    <div class="w-24 h-24 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="text-green-600 font-bold text-3xl">
                            {{ substr($profile->name, 0, 2) }}
                        </span>
                    </div>
                @endif

                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 mb-2">{{ $profile->name }}</h1>
                            <p class="text-sm text-gray-600">{{ $profile->email }}</p>
                        </div>

                        @if ($profile->status === 'active')
                            <span
                                class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-lg">Active</span>
                        @elseif($profile->status === 'inactive')
                            <span
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-bold rounded-lg">Inactive</span>
                        @else
                            <span
                                class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 text-sm font-bold rounded-lg">Suspended</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-4 gap-6 mt-6">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Age</p>
                            <p class="text-lg font-bold text-gray-900">{{ $profile->age }} years</p>
                        </div>
                        @if ($profile->blood_type)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Blood Type</p>
                                <p class="text-lg font-bold text-red-600">{{ $profile->blood_type }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500 mb-1">QR Codes</p>
                            <p class="text-lg font-bold text-gray-900">{{ $profile->getQrCodesCount() }}</p>
                        </div>
                        @if ($profile->last_login_at)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Last Login</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $profile->last_login_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Important Note -->
                @if ($profile->important_note)
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h3 class="text-lg font-bold text-yellow-900 mb-2">Important Emergency Note</h3>
                                <p class="text-yellow-800">{{ $profile->important_note }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Contact Information -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Email:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $profile->email }}</span>
                        </div>
                        @if ($profile->phone)
                            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Phone:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $profile->phone }}</span>
                            </div>
                        @endif
                        @if ($profile->address)
                            <div class="flex items-start justify-between py-3">
                                <span class="text-sm text-gray-600">Address:</span>
                                <span class="text-sm font-semibold text-gray-900 text-right max-w-xs">{{ $profile->address }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Physical Information -->
                @if ($profile->height || $profile->weight || $profile->blood_type)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Physical Information</h3>
                        <div class="grid grid-cols-3 gap-4">
                            @if ($profile->height)
                                <div class="text-center p-4 bg-gray-50 rounded-xl">
                                    <p class="text-2xl font-black text-gray-900">{{ $profile->height }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Height (cm)</p>
                                </div>
                            @endif
                            @if ($profile->weight)
                                <div class="text-center p-4 bg-gray-50 rounded-xl">
                                    <p class="text-2xl font-black text-gray-900">{{ $profile->weight }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Weight (kg)</p>
                                </div>
                            @endif
                            @if ($profile->bmi)
                                <div class="text-center p-4 bg-gray-50 rounded-xl">
                                    <p class="text-2xl font-black text-gray-900">{{ $profile->bmi }}</p>
                                    <p class="text-xs text-gray-600 mt-1">BMI</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Emergency Contacts -->
                @if ($profile->emergency_contacts && count($profile->emergency_contacts) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Emergency Contacts</h3>
                        <div class="space-y-3">
                            @foreach ($profile->emergency_contacts_formatted as $contact)
                                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $contact['name'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $contact['phone'] }}</p>
                                        </div>
                                        <span
                                            class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-lg">Emergency</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Medical Information -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Medical Information</h3>

                    @if ($profile->allergies)
                        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                            <p class="text-sm font-bold text-yellow-900 mb-2">Allergies</p>
                            <p class="text-sm text-yellow-800">{{ $profile->allergies }}</p>
                        </div>
                    @endif

                    @if ($profile->chronic_conditions && count($profile->chronic_conditions) > 0)
                        <div class="mb-4">
                            <p class="text-sm font-bold text-gray-900 mb-2">Chronic Conditions</p>
                            <div class="space-y-2">
                                @foreach ($profile->chronic_conditions_formatted as $condition)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-semibold text-gray-900">{{ $condition['name'] }}</span>
                                        @if ($condition['status'])
                                            <span
                                                class="text-xs text-gray-600 px-2 py-1 bg-white rounded">{{ $condition['status'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($profile->current_medications && count($profile->current_medications) > 0)
                        <div class="mb-4">
                            <p class="text-sm font-bold text-gray-900 mb-2">Current Medications</p>
                            <div class="space-y-2">
                                @foreach ($profile->current_medications_formatted as $medication)
                                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="font-semibold text-gray-900">{{ $medication['name'] }}</p>
                                        @if ($medication['dosage'] || $medication['frequency'])
                                            <p class="text-xs text-gray-600 mt-1">
                                                @if ($medication['dosage'])
                                                    {{ $medication['dosage'] }}
                                                @endif
                                                @if ($medication['frequency'])
                                                    - {{ $medication['frequency'] }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($profile->medical_history)
                        <div>
                            <p class="text-sm font-bold text-gray-900 mb-2">Medical History</p>
                            <p class="text-sm text-gray-700 p-4 bg-gray-50 rounded-lg">{{ $profile->medical_history }}</p>
                        </div>
                    @endif
                </div>

                <!-- Medical Files -->
                @if ($profile->medical_files && count($profile->medical_files) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Medical Files</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($profile->medical_files as $index => $file)
                                <a href="{{ Storage::url($file) }}" target="_blank"
                                    class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-all">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">File {{ $index + 1 }}</p>
                                        <p class="text-xs text-gray-500">Click to view</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Linked QR Codes -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Linked QR Codes</h3>

                    @if ($profile->hasQrCodes())
                        <div class="space-y-3">
                            @foreach ($profile->qrCodes as $qrCode)
                                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-mono font-bold text-green-700">{{ $qrCode->code }}</span>
                                        <span
                                            class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded">Active</span>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-2">
                                        Linked: {{ $qrCode->pivot->linked_at }}
                                    </p>
                                    <a href="{{ route('qr-codes.show', $qrCode) }}"
                                        class="text-xs text-green-600 hover:text-green-800 font-medium">
                                        View QR Code →
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <a href="{{ route('profiles.manage-qr-codes', $profile) }}"
                            class="mt-4 flex items-center justify-center gap-2 w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all">
                            Manage QR Codes
                        </a>
                    @else
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <p class="text-sm text-gray-600 mb-4">No QR codes linked</p>
                            <a href="{{ route('profiles.manage-qr-codes', $profile) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all text-sm">
                                Link QR Code
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Profile Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Profile Info</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600">Created:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $profile->created_at->format('M d, Y') }}</span>
                        </div>
                        @if ($profile->last_login_at)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Last Login:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $profile->last_login_at->format('M d, Y') }}</span>
                            </div>
                        @endif
                        @if ($profile->date_of_birth)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600">Date of Birth:</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $profile->date_of_birth->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>