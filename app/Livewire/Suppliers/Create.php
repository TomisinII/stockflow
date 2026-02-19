<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $company_name = '';
    public $contact_person = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $zip_code = '';
    public $country = 'Nigeria';
    public $payment_terms = '';
    public $status = 'active';
    public $notes = '';

    public $paymentTermsOptions = [
        'Net 30',
        'Net 45',
        'Net 60',
        'Net 90',
        'COD',
        'Due on Receipt',
        'Net 15',
    ];

    protected NotificationService $notificationService;

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'zip_code' => 'nullable|string|max:20',
        'country' => 'required|string|max:100',
        'payment_terms' => 'nullable|string|max:100',
        'status' => 'required|in:active,inactive',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'company_name.required' => 'Company name is required',
        'email.email' => 'Please enter a valid email address',
        'country.required' => 'Country is required',
    ];

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function save()
    {
        $this->validate();

        $this->authorize('create', Supplier::class);

        $supplier = Supplier::create([
            'company_name' => $this->company_name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'payment_terms' => $this->payment_terms,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        // Notify admins and managers about new supplier
        $this->notificationService->notifyAdminsAndManagers(
            type: 'info',
            title: 'New Supplier Added',
            message: "{$supplier->company_name} has been added as a new supplier by " . Auth::user()->name . ".",
            data: [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->company_name,
                'created_by' => Auth::user()->name,
                'link' => route('suppliers.show', $supplier),
            ]
        );

        $this->dispatch('notification-created');
        $this->dispatch('supplier-created');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->reset();
        $this->dispatch('close-modal', 'create-supplier');
    }

    public function render()
    {
        return view('livewire.suppliers.create');
    }
}
