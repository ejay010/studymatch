<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\DigitalResource;
use Illuminate\Support\Facades\Auth;

class EducatorDigitalResources extends Component
{
    use WithFileUploads;

    public $file;
    public string $title = '';
    public string $description = '';
    public string $subject = 'Math';
    public ?int $grade_level = 5;
    public $price = 0.00;

    public bool $showUploadForm = false;
    public ?string $successMessage = null;

    public function rules()
    {
        return [
            'title' => 'required|string|min:3|max:200',
            'description' => 'nullable|string|max:1000',
            'subject' => 'required|string|max:100',
            'grade_level' => 'nullable|integer|min:1|max:12',
            'price' => 'required|numeric|min:0|max:200',
            'file' => 'required|file|max:25600',
        ];
    }

    public function save()
    {
        $this->validate();

        $educator = Auth::user()->educatorProfile;
        if (!$educator) {
            return;
        }

        $originalName = $this->file->getClientOriginalName();
        $extension = strtolower($this->file->getClientOriginalExtension());
        $size = $this->file->getSize();

        // Store privately inside resources directory
        $storedPath = $this->file->store('resources', 'local');

        DigitalResource::create([
            'educator_profile_id' => $educator->id,
            'title' => $this->title,
            'description' => $this->description,
            'subject' => $this->subject,
            'grade_level' => $this->grade_level ?: null,
            'price' => $this->price,
            'file_path' => $storedPath,
            'file_name' => $originalName,
            'file_size' => $size,
            'file_type' => $extension,
            'downloads_count' => 0,
        ]);

        $this->reset(['title', 'description', 'price', 'file']);
        $this->showUploadForm = false;
        $this->successMessage = "Digital resource uploaded and published to the marketplace!";
    }

    public function render()
    {
        $educator = Auth::user()->educatorProfile;
        $resources = $educator
            ? DigitalResource::where('educator_profile_id', $educator->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('livewire.educator-digital-resources', [
            'resources' => $resources,
        ]);
    }
}
