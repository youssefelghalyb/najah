<x-dashboard-layout>
    <x-slot name="title">Profiles</x-slot>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Emergency Profiles</h2>
                <p class="text-sm text-gray-600 mt-1">Manage customer emergency profiles</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('profiles.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-green-500/30 hover:shadow-xl hover:shadow-green-500/40">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Profile
                </a>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
            <form method="GET" action="{{ route('profiles.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, email, phone, or UUID..."
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                <select name="status"
                    class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended
                    </option>
                </select>
                <button type="submit"
                    class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl transition-all">
                    Search
                </button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('profiles.index') }}"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Profiles Table -->
        @if ($profiles->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Profile</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Contact</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Medical Info</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">QR
                                    Codes</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Created</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($profiles as $profile)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($profile->profile_image)
                                                <img src="{{ Storage::url($profile->profile_image) }}"
                                                    alt="{{ $profile->name }}"
                                                    class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                                    <span class="text-green-600 font-bold text-sm">
                                                        {{ substr($profile->name, 0, 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $profile->name }}</div>
                                                <div class="text-xs text-gray-500">Age: {{ $profile->age }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <div class="text-gray-900">{{ $profile->email }}</div>
                                            @if ($profile->phone)
                                                <div class="text-gray-500">{{ $profile->phone }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            @if ($profile->blood_type)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded">
                                                    {{ $profile->blood_type }}
                                                </span>
                                            @endif
                                            @if ($profile->emergency_contacts)
                                                <div class="text-xs text-gray-600">
                                                    {{ count($profile->emergency_contacts) }} Emergency Contacts
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if ($profile->hasQrCodes())
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-lg">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                    </svg>
                                                    {{ $profile->getQrCodesCount() }} QR
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg">
                                                    No QR
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($profile->status === 'active')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-lg">Active</span>
                                        @elseif($profile->status === 'inactive')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg">Inactive</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-lg">Suspended</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $profile->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('profiles.show', $profile) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-all text-xs">
                                                View
                                            </a>
                                            <a href="{{ route('profiles.edit', $profile) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all text-xs">
                                                Edit
                                            </a>
                                            <a href="{{ route('profiles.manage-qr-codes', $profile) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all text-xs">
                                                QR Codes
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $profiles->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No profiles found</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first emergency profile</p>
                <a href="{{ route('profiles.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Profile
                </a>
            </div>
        @endif
    </div>
</x-dashboard-layout>