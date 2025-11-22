<x-dashboard-layout>
    <x-slot name="title">Edit Profile - {{ $profile->name }}</x-slot>

    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('profiles.show', $profile) }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Profile
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200 bg-gradient-to-r from-blue-50 to-transparent p-6">
                <h2 class="text-2xl font-bold text-gray-900">Edit Profile</h2>
                <p class="text-sm text-gray-600 mt-1">Update profile information for {{ $profile->name }}</p>
            </div>

            <form action="{{ route('profiles.update', $profile) }}" method="POST" enctype="multipart/form-data"
                class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column - Basic Information -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Basic Information Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 pb-3 border-b border-gray-200 mb-6">Basic
                                Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name
                                        *</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $profile->name) }}" required
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Age -->
                                <div>
                                    <label for="age" class="block text-sm font-semibold text-gray-700 mb-2">Age
                                        *</label>
                                    <input type="number" name="age" id="age"
                                        value="{{ old('age', $profile->age) }}" min="1" max="150"
                                        required
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('age') border-red-500 @enderror">
                                    @error('age')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Date of Birth -->
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-2">Date
                                        of Birth</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth"
                                        value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email
                                        *</label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $profile->email) }}" required
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">New
                                        Password</label>
                                    <input type="password" name="password" id="password"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone
                                        Number</label>
                                    <input type="tel" name="phone" id="phone"
                                        value="{{ old('phone', $profile->phone) }}"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                    <select name="status" id="status"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="active"
                                            {{ old('status', $profile->status) == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $profile->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="suspended"
                                            {{ old('status', $profile->status) == 'suspended' ? 'selected' : '' }}>
                                            Suspended</option>
                                    </select>
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <label for="address"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                                    <textarea name="address" id="address" rows="2"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $profile->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Physical Information Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 pb-3 border-b border-gray-200 mb-6">Physical
                                Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Height -->
                                <div>
                                    <label for="height" class="block text-sm font-semibold text-gray-700 mb-2">Height
                                        (cm)</label>
                                    <input type="number" name="height" id="height"
                                        value="{{ old('height', $profile->height) }}" step="0.01" min="0"
                                        max="300"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Weight -->
                                <div>
                                    <label for="weight" class="block text-sm font-semibold text-gray-700 mb-2">Weight
                                        (kg)</label>
                                    <input type="number" name="weight" id="weight"
                                        value="{{ old('weight', $profile->weight) }}" step="0.01" min="0"
                                        max="500"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Blood Type -->
                                <div>
                                    <label for="blood_type" class="block text-sm font-semibold text-gray-700 mb-2">Blood
                                        Type</label>
                                    <select name="blood_type" id="blood_type"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Blood Type</option>
                                        <option value="A+"
                                            {{ old('blood_type', $profile->blood_type) == 'A+' ? 'selected' : '' }}>A+
                                        </option>
                                        <option value="A-"
                                            {{ old('blood_type', $profile->blood_type) == 'A-' ? 'selected' : '' }}>A-
                                        </option>
                                        <option value="B+"
                                            {{ old('blood_type', $profile->blood_type) == 'B+' ? 'selected' : '' }}>B+
                                        </option>
                                        <option value="B-"
                                            {{ old('blood_type', $profile->blood_type) == 'B-' ? 'selected' : '' }}>B-
                                        </option>
                                        <option value="AB+"
                                            {{ old('blood_type', $profile->blood_type) == 'AB+' ? 'selected' : '' }}>AB+
                                        </option>
                                        <option value="AB-"
                                            {{ old('blood_type', $profile->blood_type) == 'AB-' ? 'selected' : '' }}>AB-
                                        </option>
                                        <option value="O+"
                                            {{ old('blood_type', $profile->blood_type) == 'O+' ? 'selected' : '' }}>O+
                                        </option>
                                        <option value="O-"
                                            {{ old('blood_type', $profile->blood_type) == 'O-' ? 'selected' : '' }}>O-
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 pb-3 border-b border-gray-200 mb-6">Medical
                                Information</h3>

                            <!-- Allergies -->
                            <div class="mb-6">
                                <label for="allergies"
                                    class="block text-sm font-semibold text-gray-700 mb-2">Allergies</label>
                                <textarea name="allergies" id="allergies" rows="3" placeholder="List any allergies..."
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('allergies', $profile->allergies) }}</textarea>
                            </div>

                            <!-- Emergency Contacts -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">Emergency Contacts (Up to
                                        4)</label>
                                    <button type="button" onclick="addEmergencyContact()"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Contact
                                    </button>
                                </div>
                                <div id="emergencyContactsContainer" class="space-y-3">
                                    @if ($profile->emergency_contacts)
                                        @foreach ($profile->emergency_contacts as $index => $contact)
                                            <div class="emergency-contact-item p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                                <div class="flex items-start justify-between mb-3">
                                                    <h4 class="font-semibold text-gray-900">Contact #{{ $index + 1 }}</h4>
                                                    <button type="button" onclick="this.closest('.emergency-contact-item').remove()" class="text-red-600 hover:text-red-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <input type="text" name="emergency_contacts[{{ $index }}][name]" placeholder="Name" value="{{ $contact['name'] ?? '' }}" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <input type="tel" name="emergency_contacts[{{ $index }}][phone]" placeholder="Phone" value="{{ $contact['phone'] ?? '' }}" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Chronic Conditions -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">Chronic Conditions (Up to
                                        5)</label>
                                    <button type="button" onclick="addChronicCondition()"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Condition
                                    </button>
                                </div>
                                <div id="chronicConditionsContainer" class="space-y-3">
                                    @if ($profile->chronic_conditions)
                                        @foreach ($profile->chronic_conditions as $index => $condition)
                                            <div class="chronic-condition-item p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                                <div class="flex items-start justify-between mb-3">
                                                    <h4 class="font-semibold text-gray-900">Condition #{{ $index + 1 }}</h4>
                                                    <button type="button" onclick="this.closest('.chronic-condition-item').remove()" class="text-red-600 hover:text-red-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <input type="text" name="chronic_conditions[{{ $index }}][name]" placeholder="Condition Name" value="{{ $condition['name'] ?? '' }}" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <input type="text" name="chronic_conditions[{{ $index }}][status]" placeholder="Status" value="{{ $condition['status'] ?? '' }}" class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Current Medications -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">Current Medications (Up to
                                        10)</label>
                                    <button type="button" onclick="addMedication()"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Medication
                                    </button>
                                </div>
                                <div id="medicationsContainer" class="space-y-3">
                                    @if ($profile->current_medications)
                                        @foreach ($profile->current_medications as $index => $medication)
                                            <div class="medication-item p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                                <div class="flex items-start justify-between mb-3">
                                                    <h4 class="font-semibold text-gray-900">Medication #{{ $index + 1 }}</h4>
                                                    <button type="button" onclick="this.closest('.medication-item').remove()" class="text-red-600 hover:text-red-800">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="grid grid-cols-3 gap-3">
                                                    <input type="text" name="current_medications[{{ $index }}][name]" placeholder="Name" value="{{ $medication['name'] ?? '' }}" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <input type="text" name="current_medications[{{ $index }}][dosage]" placeholder="Dosage" value="{{ $medication['dosage'] ?? '' }}" class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <input type="text" name="current_medications[{{ $index }}][frequency]" placeholder="Frequency" value="{{ $medication['frequency'] ?? '' }}" class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Medical History -->
                            <div class="mb-6">
                                <label for="medical_history" class="block text-sm font-semibold text-gray-700 mb-2">Medical
                                    History</label>
                                <textarea name="medical_history" id="medical_history" rows="4"
                                    placeholder="Describe medical history, past surgeries, etc..."
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('medical_history', $profile->medical_history) }}</textarea>
                            </div>

                            <!-- Important Note -->
                            <div>
                                <label for="important_note" class="block text-sm font-semibold text-gray-700 mb-2">Important
                                    Emergency Note</label>
                                <textarea name="important_note" id="important_note" rows="3"
                                    placeholder="Ex: انا مريض سكر و سكر عندي بيعلي كتير لو لقيتني واقع قيس سكري"
                                    class="w-full px-4 py-2.5 bg-yellow-50 border border-yellow-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-500">{{ old('important_note', $profile->important_note) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Media -->
                    <div class="space-y-6">
                        <!-- Current Profile Image -->
                        @if ($profile->profile_image)
                            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Current Profile Image</h3>
                                <img src="{{ Storage::url($profile->profile_image) }}" alt="{{ $profile->name }}"
                                    class="w-full rounded-lg mb-3">
                                <p class="text-xs text-gray-500">Upload a new image to replace this one</p>
                            </div>
                        @endif

                        <!-- Profile Image Upload -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                {{ $profile->profile_image ? 'New Profile Image' : 'Profile Image' }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-center w-full">
                                    <label for="profile_image"
                                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to
                                                    upload</span></p>
                                            <p class="text-xs text-gray-500">PNG, JPG (MAX. 2MB)</p>
                                        </div>
                                        <input id="profile_image" name="profile_image" type="file" class="hidden"
                                            accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Files Upload -->
                        <div class="bg-white rounded-2xl border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Add Medical Files</h3>
                            <div class="space-y-4">
                                @if ($profile->medical_files && count($profile->medical_files) > 0)
                                    <div class="mb-4">
                                        <p class="text-sm font-semibold text-gray-700 mb-2">Current Files:
                                            {{ count($profile->medical_files) }}</p>
                                    </div>
                                @endif
                                <div class="flex items-center justify-center w-full">
                                    <label for="medical_files"
                                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-2 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-xs text-gray-500">PDF, JPG, PNG (MAX. 5MB each)</p>
                                        </div>
                                        <input id="medical_files" name="medical_files[]" type="file" class="hidden"
                                            accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-gray-200">
                    <a href="{{ route('profiles.show', $profile) }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let emergencyContactIndex = {{ $profile->emergency_contacts ? count($profile->emergency_contacts) : 0 }};
            let chronicConditionIndex = {{ $profile->chronic_conditions ? count($profile->chronic_conditions) : 0 }};
            let medicationIndex = {{ $profile->current_medications ? count($profile->current_medications) : 0 }};

            function addEmergencyContact() {
                if (document.querySelectorAll('.emergency-contact-item').length >= 4) {
                    alert('Maximum 4 emergency contacts allowed');
                    return;
                }

                const html = `
                <div class="emergency-contact-item p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Contact #${emergencyContactIndex + 1}</h4>
                        <button type="button" onclick="this.closest('.emergency-contact-item').remove()" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="emergency_contacts[${emergencyContactIndex}][name]" placeholder="Name" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="tel" name="emergency_contacts[${emergencyContactIndex}][phone]" placeholder="Phone" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            `;

                document.getElementById('emergencyContactsContainer').insertAdjacentHTML('beforeend', html);
                emergencyContactIndex++;
            }

            function addChronicCondition() {
                if (document.querySelectorAll('.chronic-condition-item').length >= 5) {
                    alert('Maximum 5 chronic conditions allowed');
                    return;
                }

                const html = `
                <div class="chronic-condition-item p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Condition #${chronicConditionIndex + 1}</h4>
                        <button type="button" onclick="this.closest('.chronic-condition-item').remove()" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="chronic_conditions[${chronicConditionIndex}][name]" placeholder="Condition Name" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="chronic_conditions[${chronicConditionIndex}][status]" placeholder="Status" class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            `;

                document.getElementById('chronicConditionsContainer').insertAdjacentHTML('beforeend', html);
                chronicConditionIndex++;
            }

            function addMedication() {
                if (document.querySelectorAll('.medication-item').length >= 10) {
                    alert('Maximum 10 medications allowed');
                    return;
                }

                const html = `
                <div class="medication-item p-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <div class="flex items-start justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Medication #${medicationIndex + 1}</h4>
                        <button type="button" onclick="this.closest('.medication-item').remove()" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="text" name="current_medications[${medicationIndex}][name]" placeholder="Name" required class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="current_medications[${medicationIndex}][dosage]" placeholder="Dosage" class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="current_medications[${medicationIndex}][frequency]" placeholder="Frequency" class="px-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            `;

                document.getElementById('medicationsContainer').insertAdjacentHTML('beforeend', html);
                medicationIndex++;
            }
        </script>
    @endpush
</x-dashboard-layout>