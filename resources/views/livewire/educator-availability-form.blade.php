<div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Weekly Calendar Availability</h3>
            <p class="text-sm text-gray-600">Set your recurring weekly teaching hours. Parents can only book 1-on-1 sessions during your active times.</p>
        </div>
    </div>

    @if($isSaved)
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm font-medium flex items-center justify-between">
            <span>✓ Availability schedule updated successfully!</span>
            <button type="button" wire:click="$set('isSaved', false)" class="text-green-600 hover:text-green-900 text-xs">Dismiss</button>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div class="divide-y divide-gray-100">
            @foreach($days as $day => $settings)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 w-40">
                        <input 
                            type="checkbox" 
                            id="day_{{ $day }}" 
                            wire:model.live="days.{{ $day }}.is_available" 
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 h-4 w-4"
                        >
                        <label for="day_{{ $day }}" class="font-medium text-sm text-gray-800 cursor-pointer select-none">
                            {{ $dayNames[$day] ?? 'Day ' . $day }}
                        </label>
                    </div>

                    @if($settings['is_available'])
                        <div class="flex items-center gap-2">
                            <input 
                                type="time" 
                                wire:model="days.{{ $day }}.start_time" 
                                class="rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 py-1.5 px-3"
                            >
                            <span class="text-gray-400 text-sm">to</span>
                            <input 
                                type="time" 
                                wire:model="days.{{ $day }}.end_time" 
                                class="rounded-md border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500 py-1.5 px-3"
                            >
                        </div>
                    @else
                        <span class="text-sm text-gray-400 italic">Unavailable / Day Off</span>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <button 
                type="submit" 
                class="bg-blue-600 text-white px-5 py-2 rounded-md font-semibold text-sm hover:bg-blue-700 transition shadow-sm"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="save">Save Availability</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
