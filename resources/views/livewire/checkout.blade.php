<?php

use Livewire\Volt\Component;
use App\Agents\CommerceAgent;
use App\Models\EducatorProfile;

new class extends Component
{
    public $educator;
    public ?string $errorMessage = null;
    public bool $loading = false;

    public function mount(?EducatorProfile $educator = null)
    {
        if ($educator) {
            $this->educator = $educator;
        } else {
            // Fallback for testing on the home page sandbox
            $this->educator = EducatorProfile::with('user')->first();
        }
    }

    public function checkout()
    {
        $this->loading = true;
        $this->errorMessage = null;

        try {
            $agent = new CommerceAgent();
            
            $result = $agent->processTransaction(
                buyerId: auth()->id() ?? 1, 
                purchasable: $this->educator, 
                amount: (float)$this->educator->hourly_rate, 
                educator: $this->educator
            );

            if ($result['checkout_url']) {
                return redirect()->away($result['checkout_url']);
            } else {
                $this->errorMessage = "Could not generate PayPal checkout link.";
            }

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->loading = false;
    }
};
?>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-4">Checkout (Test)</h2>
    @if(!$educator)
        <p class="text-red-600">No educator found. Please run the seeder first.</p>
    @else
        <div class="bg-gray-50 p-6 rounded-md border border-gray-200 mb-6 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold">1-on-1 Session with {{ $educator->user->name ?? 'Educator' }}</h3>
                <p class="text-gray-600 text-sm mt-1">1 Hour Tutoring Session</p>
            </div>
            <div class="text-2xl font-extrabold text-gray-900">
                ${{ number_format($educator->hourly_rate, 2) }}
            </div>
        </div>

        @if($errorMessage)
            <div class="p-4 bg-red-100 text-red-800 rounded-md mb-6 break-words">
                <strong>Error:</strong> {{ $errorMessage }}
            </div>
        @endif

        <div class="flex justify-end">
            <button 
                wire:click="checkout" 
                class="bg-blue-600 text-white px-8 py-4 rounded-md hover:bg-blue-700 transition font-bold text-lg w-full flex justify-center items-center gap-2"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="checkout">Pay with PayPal</span>
                <span wire:loading wire:target="checkout">Processing...</span>
            </button>
        </div>
    @endif
</div>
