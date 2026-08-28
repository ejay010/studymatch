<div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
    <h3 class="text-lg font-bold mb-4">Student Profile</h3>
    <p class="text-sm text-gray-600 mb-6">Tell us about your student so we can recommend the best educators and classes.</p>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label for="grade_level" class="block text-sm font-medium text-gray-700">Grade Level</label>
            <select wire:model="grade_level" id="grade_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Select Grade</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">Grade {{ $i }}</option>
                @endfor
            </select>
            @error('grade_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="subjects_of_interest" class="block text-sm font-medium text-gray-700">Subjects of Interest (Comma-separated)</label>
            <input type="text" wire:model="subjects_of_interest" id="subjects_of_interest" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Math, Science, Coding">
            @error('subjects_of_interest') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="learning_styles" class="block text-sm font-medium text-gray-700">Learning Styles (Comma-separated)</label>
            <input type="text" wire:model="learning_styles" id="learning_styles" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. Visual, Hands-on, Fast-paced">
            @error('learning_styles') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="pt-4 flex items-center justify-end">
            @if($isSaved)
                <span class="text-green-600 text-sm font-medium mr-4">Saved!</span>
            @endif
            <button type="submit" class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Student Profile</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
