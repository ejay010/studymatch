<div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Small-Group Cohort Classes</h3>
            <p class="text-sm text-gray-600">Host small group classes (e.g. 4–6 students) for homeschool cohorts or enrichment.</p>
        </div>
        <button 
            type="button" 
            wire:click="$toggle('showCreateForm')"
            class="bg-blue-600 text-white px-4 py-2 rounded-md font-semibold text-sm hover:bg-blue-700 transition shadow-sm"
        >
            {{ $showCreateForm ? 'Cancel' : '+ Create Group Class' }}
        </button>
    </div>

    @if($successMessage)
        <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm font-medium flex items-center justify-between">
            <span>✓ {{ $successMessage }}</span>
            <button type="button" wire:click="$set('successMessage', null)" class="text-green-600 hover:text-green-900 text-xs">Dismiss</button>
        </div>
    @endif

    <!-- Create Class Form -->
    @if($showCreateForm)
        <form wire:submit.prevent="save" class="bg-gray-50 p-6 rounded-xl border border-gray-200 space-y-4">
            <h4 class="font-bold text-gray-900 text-base">New Group Class Details</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Class Title</label>
                    <input type="text" wire:model="title" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. Grade 4 Math Olympiad Cohort">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Subject</label>
                    <select wire:model="subject" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Math">Math</option>
                        <option value="Science">Science</option>
                        <option value="English & Reading">English & Reading</option>
                        <option value="Coding & Tech">Coding & Tech</option>
                        <option value="History & Social Studies">History & Social Studies</option>
                        <option value="Art & Music">Art & Music</option>
                    </select>
                    @error('subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Target Grade Level</label>
                    <select wire:model="grade_level" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">Grade {{ $i }}</option>
                        @endfor
                    </select>
                    @error('grade_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Price per Student (USD)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 text-sm">$</span>
                        </div>
                        <input type="number" step="0.50" wire:model="price" class="w-full text-sm rounded-md border-gray-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Max Capacity (Students)</label>
                    <input type="number" min="2" max="30" wire:model="max_capacity" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="text-xs text-gray-500">Recommended: 4 to 6 students</span>
                    @error('max_capacity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Start Date & Time</label>
                    <input type="datetime-local" wire:model="starts_at" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('starts_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Duration (Minutes)</label>
                    <input type="number" step="15" min="15" max="180" wire:model="duration_minutes" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('duration_minutes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Class Description / Syllabus</label>
                    <textarea rows="3" wire:model="description" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="What will students learn in this session?"></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" wire:click="$set('showCreateForm', false)" class="text-sm text-gray-600 hover:text-gray-900 px-3 py-2">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md font-semibold text-sm hover:bg-blue-700 transition" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Publish Class</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    @endif

    <!-- Classes Listing -->
    @if($classes->isEmpty())
        <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
            <div class="text-3xl mb-2">👥</div>
            <h4 class="font-medium text-gray-900 mb-1">No group classes yet</h4>
            <p class="text-sm text-gray-500 mb-4">Create small group cohorts to teach multiple students simultaneously.</p>
            <button wire:click="$set('showCreateForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-700 transition">
                Create Your First Cohort
            </button>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach($classes as $class)
                <div class="py-4 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-gray-900 text-base">{{ $class->title }}</h4>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-indigo-50 text-indigo-700">
                                    {{ $class->subject }}
                                </span>
                                @if($class->grade_level)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                        Grade {{ $class->grade_level }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                📅 {{ $class->starts_at->format('l, F j, Y \a\t g:i A') }} ({{ $class->duration_minutes }} mins) &bull; 
                                <span class="font-semibold text-gray-900">${{ number_format($class->price, 2) }}</span> / student
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                {{ $class->is_full ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $class->enrolled_count }} / {{ $class->max_capacity }} Enrolled
                            </span>

                            <button 
                                type="button" 
                                wire:click="toggleRoster({{ $class->id }})" 
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium underline"
                            >
                                {{ $selectedClassRosterId === $class->id ? 'Hide Roster' : 'View Roster' }}
                            </button>
                        </div>
                    </div>

                    <!-- Enrolled Roster Sub-view -->
                    @if($selectedClassRosterId === $class->id)
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mt-2 space-y-2">
                            <div class="flex justify-between items-center text-xs font-bold text-gray-700 uppercase">
                                <span>Student Roster ({{ $class->enrolled_count }} of {{ $class->max_capacity }})</span>
                                @if($class->meeting_link)
                                    <a href="{{ $class->meeting_link }}" target="_blank" class="text-blue-600 lowercase font-medium hover:underline">🎥 {{ $class->meeting_link }}</a>
                                @endif
                            </div>

                            @if($class->enrollments->isEmpty())
                                <p class="text-xs text-gray-500 italic">No students enrolled yet. This class is visible on the public /classes catalog.</p>
                            @else
                                <div class="divide-y divide-gray-200">
                                    @foreach($class->enrollments as $enrollment)
                                        <div class="py-2 flex justify-between items-center text-xs">
                                            <span class="font-semibold text-gray-900">
                                                {{ $enrollment->user?->name }}'s Student
                                            </span>
                                            <span class="text-gray-500">
                                                Enrolled {{ $enrollment->created_at->format('M j, Y') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
