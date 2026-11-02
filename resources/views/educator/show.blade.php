<x-layouts.public :title="$educator->user->name . ' - Educator Profile'">
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Educator Details (Left / 2 cols) -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-xl p-8 border border-gray-100">
                    <div class="flex items-start justify-between border-b border-gray-100 pb-6 mb-6">
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900">{{ $educator->user->name ?? 'Educator' }}</h1>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                @if($educator->is_verified)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        ✓ StudyMatch Verified
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    ${{ number_format($educator->hourly_rate, 2) }}/hr
                                </span>
                                @if($educator->timezone)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        🌐 {{ $educator->timezone }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 mb-2">About Me</h2>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $educator->bio }}</p>
                        </div>

                        @if($educator->qualifications && count($educator->qualifications) > 0)
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 mb-2">Qualifications & Background</h2>
                                <ul class="list-disc list-inside text-gray-700 space-y-1">
                                    @foreach($educator->qualifications as $qualification)
                                        <li>{{ $qualification }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Booking Widget (Right / 1 col sticky) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm sticky top-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-1">Book a 1-on-1 Session</h3>
                    <p class="text-xs text-gray-500 mb-4">Choose a date and time slot to schedule directly with {{ $educator->user->name }}.</p>
                    <livewire:book-session :educator="$educator" />
                </div>

            </div>
        </div>
    </div>
</x-layouts.public>
