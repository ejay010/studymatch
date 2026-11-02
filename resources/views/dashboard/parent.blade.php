<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Parent Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Subscription Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ isset($studentProfile) && $studentProfile->is_premium ? 'border-purple-500' : 'border-gray-300' }}">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold">StudyMatch Premium</h3>
                        <p class="text-gray-600 text-sm mt-1">
                            @if(isset($studentProfile) && $studentProfile->is_premium)
                                You are a Premium subscriber! Enjoy priority search rankings, unlimited digital resources, and free physical worksheets.
                            @else
                                Upgrade to Premium for $9.99/mo to get priority search rankings and free digital & physical worksheets.
                            @endif
                        </p>
                    </div>
                    <div>
                        @if(isset($studentProfile) && $studentProfile->is_premium)
                            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm hover:bg-gray-50 font-semibold text-sm">Manage Subscription</button>
                        @else
                            <button class="bg-purple-600 text-white px-4 py-2 rounded shadow-sm hover:bg-purple-700 font-semibold text-sm">Upgrade Now</button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upcoming Classes & Resources (2 cols) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Tutoring & Classes Card -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Upcoming Tutoring & Classes</h3>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('home') }}" class="text-xs text-blue-600 hover:underline font-medium">Find Tutor</a>
                                <span class="text-gray-300">&bull;</span>
                                <a href="{{ route('classes.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Explore Group Classes</a>
                            </div>
                        </div>
                        
                        @if((!isset($bookings) || $bookings->isEmpty()) && (!isset($enrolledClasses) || $enrolledClasses->isEmpty()))
                            <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
                                <div class="text-4xl mb-3">📅</div>
                                <h4 class="font-medium text-gray-900 mb-1">No upcoming sessions</h4>
                                <p class="text-sm text-gray-500 mb-4">You haven't booked any 1-on-1 tutoring sessions or group classes yet.</p>
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('home') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md font-semibold text-xs hover:bg-blue-700 transition">Find a Tutor</a>
                                    <a href="{{ route('classes.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md font-semibold text-xs hover:bg-indigo-700 transition">Browse Group Classes</a>
                                </div>
                            </div>
                        @else
                            <div class="divide-y divide-gray-100">
                                <!-- 1-on-1 Bookings -->
                                @foreach($bookings as $booking)
                                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 text-base">
                                                    {{ $booking->educatorProfile?->user?->name ?? 'Educator' }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-50 text-blue-700">
                                                    1-on-1 Tutoring
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

                                <!-- Group Classes -->
                                @foreach($enrolledClasses as $class)
                                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 text-base">
                                                    {{ $class->title }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-indigo-50 text-indigo-700">
                                                    Cohort: {{ $class->subject }}
                                                </span>
                                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-green-50 text-green-700">
                                                    Enrolled
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                👨‍🏫 {{ $class->educatorProfile?->user?->name }} &bull; 
                                                📅 {{ $class->starts_at->format('l, F j, Y \a\t g:i A') }} ({{ $class->duration_minutes }} mins)
                                            </div>
                                        </div>

                                        <div>
                                            @if($class->meeting_link)
                                                <a href="{{ $class->meeting_link }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs font-bold hover:bg-indigo-700 transition">
                                                    <span>🎥 Join Classroom</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Digital Resources Card -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">My Digital Resources</h3>
                            <a href="{{ route('resources.index') }}" class="text-xs text-blue-600 hover:underline font-medium">Browse Resource Store &rarr;</a>
                        </div>

                        @if(!isset($purchasedResources) || $purchasedResources->isEmpty())
                            <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
                                <div class="text-3xl mb-2">📁</div>
                                <h4 class="font-medium text-gray-900 mb-1">No materials acquired yet</h4>
                                <p class="text-sm text-gray-500 mb-4">Browse our teacher store for practice worksheets, flashcards, and lesson plans.</p>
                                <a href="{{ route('resources.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md font-semibold text-xs hover:bg-blue-700 transition">Explore Store</a>
                            </div>
                        @else
                            <div class="divide-y divide-gray-100">
                                @foreach($purchasedResources as $resource)
                                    <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-900 text-sm">{{ $resource->title }}</span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-gray-100 text-gray-700">{{ $resource->file_type }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5">
                                                By {{ $resource->educatorProfile?->user?->name }} &bull; {{ $resource->formatted_size }}
                                            </div>
                                        </div>

                                        <a 
                                            href="{{ route('resources.download', $resource->id) }}" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                                        >
                                            <span>⬇ Download</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions / Profile (1 col) -->
                <div class="space-y-6" x-data="{ editingStudent: false }">
                    <div x-show="!editingStudent" class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">My Student</h3>
                            @if(isset($studentProfile) && $studentProfile->grade_level)
                                <button @click="editingStudent = true" class="text-sm text-blue-600 hover:underline">Edit Profile</button>
                            @endif
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-md border border-gray-200">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ auth()->user()->name }}'s Child</div>
                                    <div class="text-xs text-gray-500">
                                        @if(isset($studentProfile) && $studentProfile->grade_level)
                                            Grade {{ $studentProfile->grade_level }}
                                        @else
                                            Grade level not set
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if(!isset($studentProfile) || !$studentProfile->grade_level)
                                <button @click="editingStudent = true" class="w-full text-center px-4 py-2 bg-white hover:bg-gray-50 rounded-md transition text-sm font-medium border border-gray-200 border-dashed text-gray-600">
                                    + Set up Student Profile
                                </button>
                            @else
                                @if($studentProfile->subjects_of_interest)
                                    <div class="mt-3">
                                        <p class="text-xs font-bold text-gray-500 uppercase">Subjects</p>
                                        <p class="text-sm text-gray-700">{{ implode(', ', $studentProfile->subjects_of_interest) }}</p>
                                    </div>
                                @endif
                                @if($studentProfile->learning_styles)
                                    <div class="mt-2">
                                        <p class="text-xs font-bold text-gray-500 uppercase">Learning Style</p>
                                        <p class="text-sm text-gray-700">{{ implode(', ', $studentProfile->learning_styles) }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div x-show="editingStudent" style="display: none;">
                        <livewire:student-profile-form />
                        <div class="mt-4 flex justify-end">
                            <button @click="editingStudent = false" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
