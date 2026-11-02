<div class="space-y-8">
    
    <!-- Filter and Search Bar -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Search Resources</label>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Search worksheets, lesson plans, guides..."
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

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pricing</label>
                <select wire:model.live="priceFilter" class="w-full text-sm rounded-xl border-gray-200 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Prices</option>
                    <option value="free">Free Only</option>
                    <option value="paid">Paid Resources</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Catalog Grid -->
    @if($resources->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
            <div class="text-5xl mb-4">📚</div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No educational resources found</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6">We couldn't find any resources matching your current search or filters. Try adjusting your search query.</p>
            <button wire:click="$set('search', ''); $set('selectedSubject', ''); $set('selectedGrade', ''); $set('priceFilter', 'all')" class="bg-blue-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-blue-700 transition">
                Reset All Filters
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($resources as $resource)
                @php
                    $isPurchased = in_array($resource->id, $purchasedIds);
                    $canDownload = $resource->is_free || $isPurchased || $isPremium || (auth()->check() && auth()->user()->educatorProfile?->id === $resource->educator_profile_id);
                @endphp
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                {{ $resource->subject }}
                            </span>
                            <div class="flex items-center gap-1.5">
                                @if($resource->grade_level)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Grade {{ $resource->grade_level }}
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase bg-gray-100 text-gray-700">
                                    {{ $resource->file_type }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-extrabold text-lg text-gray-900 leading-snug">{{ $resource->title }}</h3>
                            @if($resource->description)
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $resource->description }}</p>
                            @endif
                        </div>

                        <!-- Educator Info -->
                        <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                {{ substr($resource->educatorProfile?->user?->name ?? 'E', 0, 1) }}
                            </div>
                            <div class="text-xs">
                                <span class="font-bold text-gray-800">{{ $resource->educatorProfile?->user?->name ?? 'Educator' }}</span>
                                <div class="text-gray-400">{{ $resource->formatted_size }} &bull; {{ $resource->downloads_count }} downloads</div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Price & Action -->
                    <div class="pt-5 mt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                        <div>
                            @if($resource->is_free)
                                <span class="text-lg font-extrabold text-green-600">Free</span>
                            @else
                                <span class="text-lg font-extrabold text-gray-900">${{ number_format($resource->price, 2) }}</span>
                                @if($isPremium)
                                    <span class="block text-[10px] text-purple-700 font-bold">Included with Premium</span>
                                @endif
                            @endif
                        </div>

                        <div>
                            @if($canDownload)
                                <a 
                                    href="{{ route('resources.download', $resource->id) }}" 
                                    class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-blue-700 transition shadow-sm inline-flex items-center gap-1.5"
                                >
                                    <span>⬇ Download File</span>
                                </a>
                            @else
                                <a 
                                    href="{{ route('login') }}" 
                                    class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-800 transition shadow-sm inline-flex items-center gap-1.5"
                                >
                                    <span>Get Resource (${{ number_format($resource->price, 2) }})</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
