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
                <!-- Earnings & Metrics Snapshot -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Net Monthly Earnings</div>
                        <div class="mt-2 text-3xl font-extrabold text-gray-900">${{ number_format($monthlyEarnings, 2) }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Upcoming Bookings</div>
                        <div class="mt-2 text-3xl font-extrabold text-gray-900">{{ $bookings->count() }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Hourly Rate</div>
                        <div class="mt-2 text-3xl font-extrabold text-gray-900">${{ number_format($educator->hourly_rate, 2) }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ currentTab: 'profile' }">
                    <!-- Main Content Area (2 cols) -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Availability Form Tab -->
                        <div x-show="currentTab === 'availability'" style="display: none;">
                            <livewire:educator-availability-form />
                            <div class="mt-4 flex justify-end">
                                <button @click="currentTab = 'profile'" class="text-sm text-gray-600 hover:text-gray-900 underline">Back to Overview</button>
                            </div>
                        </div>

                        <!-- Edit Profile Form Tab -->
                        <div x-show="currentTab === 'edit'" style="display: none;">
                            <livewire:educator-profile-form />
                            <div class="mt-4 flex justify-end">
                                <button @click="currentTab = 'profile'" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancel Editing</button>
                            </div>
                        </div>

                        <!-- Group Classes Tab -->
                        <div x-show="currentTab === 'classes'" style="display: none;">
                            <livewire:educator-group-classes />
                            <div class="mt-4 flex justify-end">
                                <button @click="currentTab = 'profile'" class="text-sm text-gray-600 hover:text-gray-900 underline">Back to Overview</button>
                            </div>
                        </div>

                        <!-- Digital Resources Tab -->
                        <div x-show="currentTab === 'resources'" style="display: none;">
                            <livewire:educator-digital-resources />
                            <div class="mt-4 flex justify-end">
                                <button @click="currentTab = 'profile'" class="text-sm text-gray-600 hover:text-gray-900 underline">Back to Overview</button>
                            </div>
                        </div>

                        <!-- Overview Tab -->
                        <div x-show="currentTab === 'profile'" class="space-y-6">
                            
                            <!-- Profile Summary Card -->
                            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-900">Your Public Profile</h3>
                                    <a href="{{ route('tutor.show', $educator->id) }}" target="_blank" class="text-sm text-blue-600 hover:underline inline-flex items-center gap-1 font-medium">
                                        View Public Page &rarr;
                                    </a>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="block text-xs font-semibold text-gray-500 uppercase">Hourly Rate</span>
                                            <span class="text-base font-bold text-gray-900">${{ number_format($educator->hourly_rate, 2) }}/hr</span>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-semibold text-gray-500 uppercase">Timezone</span>
                                            <span class="text-base text-gray-800">{{ $educator->timezone ?? 'America/New_York' }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="block text-xs font-semibold text-gray-500 uppercase">Membership Tier</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-blue-50 text-blue-700 mt-1">
                                                {{ $educator->tier ?? 'Free' }} Tier
                                            </span>
                                        </div>
                                        <div>
                                            <span class="block text-xs font-semibold text-gray-500 uppercase">Verification Status</span>
                                            <div class="mt-1">
                                                @if($educator->is_verified)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        ✓ StudyMatch Verified
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Pending Verification
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-semibold text-gray-500 uppercase">Bio</span>
                                        <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $educator->bio }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Upcoming Bookings Card -->
                            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Upcoming Tutoring Sessions</h3>
                                
                                @if($bookings->isEmpty())
                                    <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
                                        <div class="text-3xl mb-2">📅</div>
                                        <h4 class="font-medium text-gray-900 mb-1">No upcoming sessions</h4>
                                        <p class="text-sm text-gray-500">When parents book 1-on-1 sessions from your profile, they will appear here.</p>
                                    </div>
                                @else
                                    <div class="divide-y divide-gray-100">
                                        @foreach($bookings as $booking)
                                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-gray-900 text-base">
                                                            {{ $booking->studentProfile?->user?->name ? $booking->studentProfile->user->name . "'s Student" : 'Student Session' }}
                                                        </span>
                                                        <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-50 text-blue-700">
                                                            {{ $booking->type }}
                                                        </span>
                                                        <span class="px-2 py-0.5 text-xs font-medium rounded bg-green-50 text-green-700">
                                                            {{ ucfirst($booking->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        📅 {{ $booking->starts_at->format('l, F j, Y') }} &bull; {{ $booking->starts_at->format('g:i A') }} - {{ $booking->ends_at->format('g:i A') }}
                                                    </div>
                                                </div>

                                                <div>
                                                    @if($booking->meeting_link)
                                                        <a href="{{ $booking->meeting_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-bold hover:bg-blue-700 transition">
                                                            <span>🎥 Join Classroom</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Financial Ledger Card -->
                            <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Earnings & Payout Ledger</h3>
                                
                                @if(!isset($transactions) || $transactions->isEmpty())
                                    <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
                                        <div class="text-3xl mb-2">💳</div>
                                        <h4 class="font-medium text-gray-900 mb-1">No completed transactions</h4>
                                        <p class="text-sm text-gray-500">Your earnings from tutoring bookings, cohort classes, and digital resource sales will be logged here.</p>
                                    </div>
                                @else
                                    <div class="divide-y divide-gray-100">
                                        @foreach($transactions as $tx)
                                            <div class="py-3 flex justify-between items-center text-xs">
                                                <div>
                                                    <span class="font-bold text-gray-900 text-sm block">
                                                        {{ class_basename($tx->purchasable_type) }} #{{ $tx->purchasable_id }}
                                                    </span>
                                                    <span class="text-gray-500">
                                                        {{ $tx->created_at->format('M j, Y g:i A') }} &bull; Gross: ${{ number_format($tx->amount, 2) }}
                                                    </span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-extrabold text-green-700 text-sm block">
                                                        +${{ number_format($tx->educator_payout, 2) }}
                                                    </span>
                                                    <span class="text-gray-400 text-[10px]">
                                                        Platform Fee: ${{ number_format($tx->commission_amount, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <!-- Actions Sidebar (1 col) -->
                    <div class="space-y-6">
                        <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button 
                                    @click="currentTab = (currentTab === 'classes' ? 'profile' : 'classes')" 
                                    :class="currentTab === 'classes' ? 'bg-blue-50 border-blue-400 text-blue-700' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'"
                                    class="w-full text-left px-4 py-2.5 rounded-md transition text-sm font-medium border border-gray-200 flex items-center justify-between"
                                >
                                    <span>👥 Manage Group Classes</span>
                                    <span class="text-xs text-gray-400" x-text="currentTab === 'classes' ? 'Close' : 'View'"></span>
                                </button>

                                <button 
                                    @click="currentTab = (currentTab === 'resources' ? 'profile' : 'resources')" 
                                    :class="currentTab === 'resources' ? 'bg-blue-50 border-blue-400 text-blue-700' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'"
                                    class="w-full text-left px-4 py-2.5 rounded-md transition text-sm font-medium border border-gray-200 flex items-center justify-between"
                                >
                                    <span>📁 Upload Digital Resources</span>
                                    <span class="text-xs text-gray-400" x-text="currentTab === 'resources' ? 'Close' : 'View'"></span>
                                </button>

                                <button 
                                    @click="currentTab = (currentTab === 'availability' ? 'profile' : 'availability')" 
                                    :class="currentTab === 'availability' ? 'bg-blue-50 border-blue-400 text-blue-700' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'"
                                    class="w-full text-left px-4 py-2.5 rounded-md transition text-sm font-medium border border-gray-200 flex items-center justify-between"
                                >
                                    <span>🗓️ Sync Calendar Availability</span>
                                    <span class="text-xs text-gray-400" x-text="currentTab === 'availability' ? 'Close' : 'Edit'"></span>
                                </button>

                                <button 
                                    @click="currentTab = (currentTab === 'edit' ? 'profile' : 'edit')" 
                                    :class="currentTab === 'edit' ? 'bg-blue-50 border-blue-400 text-blue-700' : 'bg-gray-50 hover:bg-gray-100 text-gray-800'"
                                    class="w-full text-left px-4 py-2.5 rounded-md transition text-sm font-medium border border-gray-200 flex items-center justify-between"
                                >
                                    <span>✏️ Edit Profile Info</span>
                                    <span class="text-xs text-gray-400" x-text="currentTab === 'edit' ? 'Close' : 'Edit'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
