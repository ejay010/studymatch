<?php

use Livewire\Volt\Component;
use App\Agents\DiscoveryAgent;

new class extends Component
{
    public string $keyword = '';
    public string $subject = '';
    public string $gradeLevel = '';
    public array $results = [];
    public bool $loading = false;
    public ?string $errorMessage = null;
    public bool $hasSearched = false;

    public function search()
    {
        $this->loading = true;
        $this->errorMessage = null;
        $this->hasSearched = true;

        try {
            $agent = new DiscoveryAgent();
            
            // Execute traditional search
            $collection = $agent->search(
                $this->keyword, 
                $this->subject ?: null, 
                $this->gradeLevel ? (int)$this->gradeLevel : null
            );

            // Convert to array for UI display
            $this->results = $collection->toArray();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            
            if (str_contains($this->errorMessage, 'Connection refused')) {
                $this->errorMessage = "Meilisearch is not running. Please start Meilisearch or change SCOUT_DRIVER to 'database' in your .env for local testing.";
            }
        }

        $this->loading = false;
    }
};
?>

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-4">Find a Tutor (Traditional Search)</h2>
    <p class="text-gray-600 mb-6">Filter by keyword, subject, and grade level.</p>

    <form wire:submit="search" class="flex flex-col md:flex-row gap-4 mb-8">
        <input 
            type="text" 
            wire:model="keyword" 
            placeholder="Keywords (e.g., math, reading)..." 
            class="flex-1 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        
        <select wire:model="subject" class="p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Any Subject</option>
            <option value="math">Math</option>
            <option value="science">Science</option>
            <option value="english">English / Reading</option>
            <option value="history">History</option>
        </select>

        <select wire:model="gradeLevel" class="p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Any Grade</option>
            <option value="1">1st Grade</option>
            <option value="4">4th Grade</option>
            <option value="8">8th Grade</option>
            <option value="12">12th Grade</option>
        </select>

        <button 
            type="submit" 
            class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition font-semibold w-full md:w-auto"
        >
            <span wire:loading.remove wire:target="search">Search</span>
            <span wire:loading wire:target="search">Searching...</span>
        </button>
    </form>

    @if($errorMessage)
        <div class="p-4 bg-red-100 text-red-800 rounded-md mb-6">
            <strong>Error:</strong> {{ $errorMessage }}
        </div>
    @endif

    @if($hasSearched && !$loading)
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Results ({{ count($results) }})</h3>
            
            @if(count($results) === 0)
                <p class="text-gray-500">No tutors found matching your criteria. (Try adding some dummy records to the database!)</p>
            @else
                <div class="space-y-4">
                    @foreach($results as $result)
                        <div class="p-6 border rounded-md hover:border-blue-400 hover:shadow-sm transition bg-gray-50 flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-lg text-gray-900">Educator #{{ $result['id'] }}</h4>
                                <p class="text-sm text-gray-700 mt-1 max-w-xl">{{ $result['bio'] ?? 'N/A' }}</p>
                                <div class="mt-2 flex gap-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ${{ $result['hourly_rate'] ?? '0.00' }}/hr
                                    </span>
                                    @if($result['is_verified'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('tutor.show', $result['id']) }}" class="inline-block bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>