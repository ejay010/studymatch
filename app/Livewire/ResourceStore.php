<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DigitalResource;
use App\Models\ResourcePurchase;
use Illuminate\Support\Facades\Auth;

class ResourceStore extends Component
{
    public string $search = '';
    public string $selectedSubject = '';
    public string $selectedGrade = '';
    public string $priceFilter = 'all';

    public function render()
    {
        $query = DigitalResource::with(['educatorProfile.user'])
            ->orderBy('created_at', 'desc');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->selectedSubject)) {
            $query->where('subject', $this->selectedSubject);
        }

        if (!empty($this->selectedGrade)) {
            $query->where('grade_level', (int)$this->selectedGrade);
        }

        if ($this->priceFilter === 'free') {
            $query->where('price', '<=', 0);
        } elseif ($this->priceFilter === 'paid') {
            $query->where('price', '>', 0);
        }

        $resources = $query->get();

        $purchasedIds = [];
        $isPremium = false;

        if (Auth::check()) {
            $user = Auth::user();
            $purchasedIds = ResourcePurchase::where('user_id', $user->id)
                ->pluck('digital_resource_id')
                ->toArray();

            $isPremium = (bool)($user->studentProfile?->is_premium);
        }

        $subjects = ['Math', 'Science', 'English & Reading', 'Coding & Tech', 'History & Social Studies', 'Art & Music'];

        return view('livewire.resource-store', [
            'resources' => $resources,
            'subjects' => $subjects,
            'purchasedIds' => $purchasedIds,
            'isPremium' => $isPremium,
        ]);
    }
}
