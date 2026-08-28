<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Educator Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(!$educator)
                <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg mb-6 border border-yellow-200">
                    <h3 class="font-bold text-lg mb-1">Welcome to StudyMatch!</h3>
                    <p>You haven't set up your Educator Profile yet. Please complete the form below to start receiving bookings.</p>
                </div>
                
                <livewire:educator-profile-form />
            @else
                <!-- Earnings Snapshot -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Monthly Earnings</div>
                        <div class="mt-2 text-3xl font-extrabold text-gray-900">$0.00</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Upcoming Bookings</div>
                        <div class="mt-2 text-3xl font-extrabold text-gray-900">0</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Reviews</div>
                        <div class="mt-2 text-3xl font-extrabold text-gray-900">0</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ editing: false }">
                    <!-- Profile Info -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Display View -->
                        <div x-show="!editing" class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Your Profile</h3>
                                <a href="{{ route('tutor.show', $educator->id) }}" target="_blank" class="text-sm text-blue-600 hover:underline">View Public Profile</a>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Hourly Rate</label>
                                    <div class="mt-1 text-lg font-semibold text-gray-900">${{ number_format($educator->hourly_rate, 2) }}/hr</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Bio</label>
                                    <div class="mt-1 text-sm text-gray-600">{{ $educator->bio }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Verification Status</label>
                                    <div class="mt-1">
                                        @if($educator->is_verified)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending Verification
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <div x-show="editing" style="display: none;">
                            <livewire:educator-profile-form />
                            <div class="mt-4 flex justify-end">
                                <button @click="editing = false" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancel Editing</button>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-6">
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                            <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button class="w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md transition text-sm font-medium border border-gray-200">
                                    🗓️ Sync Calendar Availability
                                </button>
                                <button class="w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md transition text-sm font-medium border border-gray-200">
                                    📁 Upload Digital Resources
                                </button>
                                <button @click="editing = true" class="w-full text-left px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-md transition text-sm font-medium border border-gray-200">
                                    ✏️ Edit Profile Info
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
