<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    public Supplier $supplier;
    public $company_name;
    public $contact_person;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $country;
    public $payment_terms;
    public $status;
    public $notes;

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

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function mount($supplierId)
    {
        $this->supplier = Supplier::findOrFail($supplierId);

        $this->company_name = $this->supplier->company_name;
        $this->contact_person = $this->supplier->contact_person;
        $this->email = $this->supplier->email;
        $this->phone = $this->supplier->phone;
        $this->address = $this->supplier->address;
        $this->city = $this->supplier->city;
        $this->state = $this->supplier->state;
        $this->zip_code = $this->supplier->zip_code;
        $this->country = $this->supplier->country;
        $this->payment_terms = $this->supplier->payment_terms;
        $this->status = $this->supplier->status;
        $this->notes = $this->supplier->notes;
    }

    public function update()
    {
        $this->validate();

        // Track changes for notification
        $changes = [];
        $originalData = [
            'company_name' => $this->supplier->company_name,
            'status' => $this->supplier->status,
            'payment_terms' => $this->supplier->payment_terms,
        ];

        $this->supplier->update([
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

        // Detect significant changes
        if ($originalData['company_name'] !== $this->company_name) {
            $changes[] = "name changed from '{$originalData['company_name']}' to '{$this->company_name}'";
        }
        if ($originalData['status'] !== $this->status) {
            $changes[] = "status changed to '{$this->status}'";
        }
        if ($originalData['payment_terms'] !== $this->payment_terms && $this->payment_terms) {
            $changes[] = "payment terms updated to '{$this->payment_terms}'";
        }

        // Notify admins and managers if there were significant changes
        if (!empty($changes)) {
            $changesList = implode(', ', $changes);
            $this->notificationService->notifyAdminsAndManagers(
                type: 'info',
                title: 'Supplier Updated',
                message: "{$this->supplier->company_name} has been updated by " . Auth::user()->name . ". Changes: {$changesList}.",
                data: [
                    'supplier_id' => $this->supplier->id,
                    'supplier_name' => $this->supplier->company_name,
                    'updated_by' => Auth::user()->name,
                    'changes' => $changes,
                    'link' => route('suppliers.show', $this->supplier),
                ]
            );

            $this->dispatch('notification-created');
        }

        $this->closeModal();
        $this->dispatch('supplier-updated');
    }

    public function closeModal()
    {
        $this->dispatch('close-modal', 'edit-supplier-' . $this->supplier->id);
    }

    public function render()
    {
        return view('livewire.suppliers.edit');
    }
}
