<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Company extends Component
{
    use WithFileUploads;

    public $company_name = '';
    public $company_email = '';
    public $company_phone = '';
    public $company_website = '';
    public $company_address = '';
    public $company_logo;
    public $existing_logo = '';

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'company_email' => 'required|email|max:255',
        'company_phone' => 'nullable|string|max:50',
        'company_website' => 'nullable|url|max:255',
        'company_address' => 'nullable|string',
        'company_logo' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $settings = Auth::user()->settings;

        $this->company_name = $settings->company_name ?? '';
        $this->company_email = $settings->company_email ?? '';
        $this->company_phone = $settings->company_phone ?? '';
        $this->company_website = $settings->company_website ?? '';
        $this->company_address = $settings->company_address ?? '';
        $this->existing_logo = $settings->company_logo ?? '';
    }

    public function save()
    {
        $this->authorize('edit_settings');
        
        $this->validate();

        $settings = Auth::user()->settings;

        $data = [
            'company_name' => $this->company_name,
            'company_email' => $this->company_email,
            'company_phone' => $this->company_phone,
            'company_website' => $this->company_website,
            'company_address' => $this->company_address,
        ];

        // Handle logo upload
        if ($this->company_logo) {
            // Delete old logo if exists
            if ($settings->company_logo) {
                Storage::disk('public')->delete($settings->company_logo);
            }

            // Store new logo
            $logoPath = $this->company_logo->store('company-logos', 'public');
            $data['company_logo'] = $logoPath;
            $this->existing_logo = $logoPath;
        }

        $settings->update($data);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Company information updated successfully!'
        ]);

        // Reset the logo input
        $this->company_logo = null;
    }

    public function render()
    {
        return view('livewire.settings.company');
    }
}
