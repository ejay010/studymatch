<div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
    <h3 class="text-lg font-bold mb-4">Setup Your Educator Profile</h3>
    <p class="text-sm text-gray-600 mb-6">Complete your profile below so parents can find and book you for sessions.</p>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700">Bio / About Me</label>
            <textarea wire:model="bio" id="bio" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Tell parents about your teaching experience..."></textarea>
            @error('bio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Hourly Rate (USD)</label>
            <div class="relative mt-1 rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">$</span>
                </div>
                <input type="number" step="0.50" wire:model="hourly_rate" id="hourly_rate" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0.00">
            </div>
            @error('hourly_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
            <select wire:model="timezone" id="timezone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="America/New_York">Eastern Time (ET)</option>
                <option value="America/Chicago">Central Time (CT)</option>
                <option value="America/Denver">Mountain Time (MT)</option>
                <option value="America/Los_Angeles">Pacific Time (PT)</option>
            </select>
            @error('timezone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="qualifications" class="block text-sm font-medium text-gray-700">Qualifications (One per line)</label>
            <textarea wire:model="qualifications" id="qualifications" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="B.S. Mathematics&#10;5 Years Teaching Experience"></textarea>
            @error('qualifications') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 flex items-center justify-end">
            @if($isSaved)
                <span class="text-green-600 text-sm font-medium mr-4">Profile Saved!</span>
            @endif
            <button type="submit" class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Profile</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
