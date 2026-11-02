<div class="space-y-4">
    @if($confirmedBooking)
        <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-green-900 space-y-3">
            <div class="flex items-center gap-2 font-bold text-base text-green-800">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Session Confirmed!
            </div>
            <p class="text-sm">
                Your 1-on-1 tutoring session with <strong>{{ $educator->user->name }}</strong> is scheduled for:
            </p>
            <div class="bg-white p-3 rounded border border-green-100 text-sm space-y-1">
                <div><strong>Date:</strong> {{ $confirmedBooking->starts_at->format('l, F j, Y') }}</div>
                <div><strong>Time:</strong> {{ $confirmedBooking->starts_at->format('g:i A') }} - {{ $confirmedBooking->ends_at->format('g:i A') }}</div>
                @if($confirmedBooking->meeting_link)
                    <div class="pt-2">
                        <a href="{{ $confirmedBooking->meeting_link }}" target="_blank" class="text-blue-600 underline font-medium inline-flex items-center gap-1">
                            <span>🎥 Join Video Classroom</span>
                        </a>
                    </div>
                @endif
            </div>
            <div class="pt-2 flex justify-between items-center text-xs">
                <a href="{{ route('dashboard.parent') }}" class="text-green-700 underline font-medium">View in My Dashboard &rarr;</a>
                <button type="button" wire:click="$set('confirmedBooking', null)" class="text-gray-500 hover:text-gray-700">Book Another</button>
            </div>
        </div>
    @else
        @if($errorMessage)
            <div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-md text-xs font-medium">
                {{ $errorMessage }}
            </div>
        @endif

        <div>
            <label for="booking_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Select Date</label>
            <input 
                type="date" 
                id="booking_date" 
                wire:model.live="selectedDate" 
                min="{{ now()->format('Y-m-d') }}"
                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3"
            >
        </div>

        <div>
            <div class="flex justify-between items-center mb-1">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Available Times</label>
                <span class="text-xs text-gray-500">1-hour slots</span>
            </div>

            @if(count($slots) === 0)
                <div class="p-4 bg-gray-50 border border-dashed border-gray-200 rounded-md text-center text-xs text-gray-500">
                    No available time slots on this date. Please choose another date.
                </div>
            @else
                <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                    @foreach($slots as $slot)
                        @php
                            $slotKey = $slot['starts_at'] . '|' . $slot['ends_at'];
                            $isSelected = ($selectedSlot === $slotKey);
                        @endphp
                        <button
                            type="button"
                            wire:click="selectSlot('{{ $slotKey }}')"
                            @disabled(!$slot['is_available'])
                            class="py-2 px-2.5 text-xs font-medium rounded-md border text-center transition
                                @if(!$slot['is_available'])
                                    bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed line-through
                                @elseif($isSelected)
                                    bg-blue-600 text-white border-blue-600 shadow-sm
                                @else
                                    bg-white text-gray-700 border-gray-200 hover:border-blue-400 hover:bg-blue-50
                                @endif
                            "
                        >
                            {{ $slot['formatted_time'] }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="pt-2 border-t border-gray-100">
            <div class="flex justify-between items-center text-sm mb-3">
                <span class="text-gray-600">Rate:</span>
                <span class="font-bold text-gray-900">${{ number_format($educator->hourly_rate, 2) }} / session</span>
            </div>

            @auth
                @if(auth()->user()->role === 'parent')
                    <button 
                        type="button" 
                        wire:click="book" 
                        @disabled(!$selectedSlot)
                        class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-lg font-bold text-sm hover:bg-blue-700 transition shadow disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="book">Book 1-on-1 Session</span>
                        <span wire:loading wire:target="book">Scheduling...</span>
                    </button>
                @else
                    <div class="p-2.5 bg-yellow-50 text-yellow-800 rounded-md text-xs text-center">
                        You are logged in as an Educator. Please log in as a Parent to book.
                    </div>
                @endif
            @else
                <a 
                    href="{{ route('login') }}" 
                    class="block text-center w-full bg-blue-600 text-white py-2.5 px-4 rounded-lg font-bold text-sm hover:bg-blue-700 transition shadow"
                >
                    Log In to Book
                </a>
                <p class="text-center text-xs text-gray-500 mt-2">
                    Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 underline">Sign up</a>
                </p>
            @endauth
        </div>
    @endif
</div>
