<div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100 space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Digital Resource Store</h3>
            <p class="text-sm text-gray-600">Sell or share your lesson plans, practice worksheets, and flashcards directly with parents.</p>
        </div>
        <button 
            type="button" 
            wire:click="$toggle('showUploadForm')"
            class="bg-blue-600 text-white px-4 py-2 rounded-md font-semibold text-sm hover:bg-blue-700 transition shadow-sm"
        >
            {{ $showUploadForm ? 'Cancel' : '+ Upload Digital Resource' }}
        </button>
    </div>

    @if($successMessage)
        <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-md text-sm font-medium flex items-center justify-between">
            <span>✓ {{ $successMessage }}</span>
            <button type="button" wire:click="$set('successMessage', null)" class="text-green-600 hover:text-green-900 text-xs">Dismiss</button>
        </div>
    @endif

    <!-- Upload Form -->
    @if($showUploadForm)
        <form wire:submit.prevent="save" class="bg-gray-50 p-6 rounded-xl border border-gray-200 space-y-4">
            <h4 class="font-bold text-gray-900 text-base">Upload Teaching Material</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Resource Title</label>
                    <input type="text" wire:model="title" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 5th Grade Fractions Mastery Worksheet & Answer Key">
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
                        <option value="">All Grades / General</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">Grade {{ $i }}</option>
                        @endfor
                    </select>
                    @error('grade_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Price (USD) - Set $0.00 for Free</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 text-sm">$</span>
                        </div>
                        <input type="number" step="0.50" wire:model="price" class="w-full text-sm rounded-md border-gray-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">File (PDF, DOCX, ZIP, Image - Max 25MB)</label>
                    <input type="file" wire:model="file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <div wire:loading wire:target="file" class="text-xs text-blue-600 mt-1">Uploading preview...</div>
                    @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description</label>
                    <textarea rows="3" wire:model="description" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Describe the materials, how many pages, and instructions for parents/educators..."></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" wire:click="$set('showUploadForm', false)" class="text-sm text-gray-600 hover:text-gray-900 px-3 py-2">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md font-semibold text-sm hover:bg-blue-700 transition" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Upload & Publish</span>
                    <span wire:loading wire:target="save">Uploading...</span>
                </button>
            </div>
        </form>
    @endif

    <!-- Uploaded Resources Listing -->
    @if($resources->isEmpty())
        <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
            <div class="text-3xl mb-2">📁</div>
            <h4 class="font-medium text-gray-900 mb-1">No digital resources uploaded</h4>
            <p class="text-sm text-gray-500 mb-4">Upload study guides, worksheets, or lesson plans to generate passive revenue.</p>
            <button wire:click="$set('showUploadForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-700 transition">
                Upload Your First Resource
            </button>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach($resources as $resource)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900 text-base">{{ $resource->title }}</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-indigo-50 text-indigo-700">
                                {{ $resource->subject }}
                            </span>
                            @if($resource->grade_level)
                                <span class="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                    Grade {{ $resource->grade_level }}
                                </span>
                            @endif
                            <span class="px-2 py-0.5 text-xs font-bold uppercase rounded bg-gray-100 text-gray-600">
                                {{ $resource->file_type }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $resource->file_name }} &bull; {{ $resource->formatted_size }} &bull; 
                            <span class="font-semibold text-gray-800">{{ $resource->downloads_count }} downloads</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-sm font-extrabold text-gray-900">
                            @if($resource->is_free)
                                <span class="text-green-600">Free</span>
                            @else
                                ${{ number_format($resource->price, 2) }}
                            @endif
                        </span>

                        <a 
                            href="{{ route('resources.download', $resource->id) }}" 
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                        >
                            <span>⬇ Download</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
