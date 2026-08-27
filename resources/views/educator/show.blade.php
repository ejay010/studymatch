<x-layouts.public :title="$educator->user->name . ' - Educator Profile'">
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <div class="flex items-center justify-between border-b pb-6 mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $educator->user->name ?? 'Educator' }}</h1>
                            <div class="flex gap-2 mt-2">
                                @if($educator->is_verified)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        ✓ Verified Background Check
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    ${{ number_format($educator->hourly_rate, 2) }}/hr
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 shadow-sm w-72">
                            <h3 class="text-sm font-bold text-gray-700 mb-2">Book a 1-on-1 Session</h3>
                            <livewire:checkout :educator="$educator" />
                        </div>
                    </div>

                    <h2 class="text-xl font-bold mb-3">About Me</h2>
                    <p class="text-gray-700 leading-relaxed mb-6">
                        {{ $educator->bio }}
                    </p>

                    @if($educator->qualifications)
                    <h2 class="text-xl font-bold mb-3">Qualifications</h2>
                    <ul class="list-disc list-inside text-gray-700 mb-6 space-y-1">
                        @foreach($educator->qualifications as $qualification)
                            <li>{{ $qualification }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
