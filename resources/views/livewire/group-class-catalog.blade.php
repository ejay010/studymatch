<div class="space-y-8">
    
    <!-- Filter and Search Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Search Cohorts</label>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search by topic, keyword, or educator..."
                        class="w-full text-sm rounded-xl border-gray-200 pl-10 pr-4 py-2.5 focus:border-blue-500 focus:ring-blue-500"
                    >
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Subject</label>
                <select wire:model.live="selectedSubject" class="w-full text-sm rounded-xl border-gray-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subj)
                        <option value="{{ $subj }}">{{ $subj }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Grade Level</label>
                <select wire:model.live="selectedGrade" class="w-full text-sm rounded-xl border-gray-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Grades</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">Grade {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Alert Notices -->
    @if($enrollmentSuccess)
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm font-medium flex items-center justify-between shadow-sm">
            <span>✓ {{ $enrollmentSuccess }}</span>
            <button type="button" wire:click="$set('enrollmentSuccess', null)" class="text-green-700 hover:text-green-900 text-xs font-bold">Dismiss</button>
        </div>
    @endif

    @if($enrollmentError)
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium flex items-center justify-between shadow-sm">
            <span>⚠ {{ $enrollmentError }}</span>
            <button type="button" wire:click="$set('enrollmentError', null)" class="text-red-700 hover:text-red-900 text-xs font-bold">Dismiss</button>
        </div>
    @endif

    <!-- Catalog Grid -->
    @if($classes->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
            <div class="text-5xl mb-4">👥</div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No small group classes found</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6">We couldn't find any cohorts matching your current filters. Try resetting the subject or search query.</p>
            <button wire:click="$set('search', ''); $set('selectedSubject', ''); $set('selectedGrade', '')" class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-blue-700 transition">
                Reset All Filters
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                @php
                    $isEnrolled = in_array($class->id, $userEnrolledClassIds);
                @endphp
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                {{ $class->subject }}
                            </span>
                            @if($class->grade_level)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    Grade {{ $class->grade_level }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h3 class="font-extrabold text-lg text-gray-900 leading-snug">{{ $class->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $class->description }}</p>
                        </div>

                        <!-- Educator Info -->
                        <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm">
                                {{ substr($class->educatorProfile?->user?->name ?? 'E', 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-800">
                                    {{ $class->educatorProfile?->user?->name ?? 'Educator' }}
                                </div>
                                @if($class->educatorProfile?->is_verified)
                                    <span class="text-xs text-green-700 font-medium">✓ Verified Educator</span>
                                @endif
                            </div>
                        </div>

                        <!-- Schedule details -->
                        <div class="bg-gray-50 p-3 rounded-xl text-xs text-gray-600 space-y-1">
                            <div>📅 {{ $class->starts_at->format('l, F j, Y') }}</div>
                            <div>⏰ {{ $class->starts_at->format('g:i A') }} ({{ $class->duration_minutes }} minutes)</div>
                        </div>

                        <!-- Capacity Bar -->
                        <div>
                            <div class="flex justify-between items-center text-xs mb-1">
                                <span class="font-semibold text-gray-600">Cohort Size</span>
                                @if($class->is_full)
                                    <span class="font-bold text-red-600">Class Full ({{ $class->max_capacity }}/{{ $class->max_capacity }})</span>
                                @else
                                    <span class="font-bold text-green-700">{{ $class->remaining_spots }} spots left ({{ $class->enrolled_count }}/{{ $class->max_capacity }})</span>
                                @endif
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div 
                                    class="h-2 rounded-full {{ $class->is_full ? 'bg-red-500' : 'bg-blue-600' }}" 
                                    style="width: {{ min(100, ($class->enrolled_count / max(1, $class->max_capacity)) * 100) }}%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Price & CTA -->
                    <div class="pt-5 mt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                        <div>
                            <span class="text-xs text-gray-500 block">Tuition</span>
                            <span class="text-lg font-extrabold text-gray-900">${{ number_format($class->price, 2) }}</span>
                        </div>

                        <div>
                            @if($isEnrolled)
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    ✓ Enrolled
                                </span>
                            @elseif($class->is_full)
                                <button disabled class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-100 text-gray-400 cursor-not-allowed">
                                    Full
                                </button>
                            @else
                                <button 
                                    wire:click="enroll({{ $class->id }})" 
                                    class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-blue-700 transition shadow-sm"
                                    wire:loading.attr="disabled"
                                >
                                    <span wire:loading.remove wire:target="enroll({{ $class->id }})">Enroll Student</span>
                                    <span wire:loading wire:target="enroll({{ $class->id }})">Enrolling...</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
