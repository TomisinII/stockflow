<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ConfirmModal extends Component
{
    public bool $show = false;
    public string $title = 'Confirm Action';
    public string $message = 'Are you sure you want to proceed?';
    public string $confirmText = 'Confirm';
    public string $confirmColor = 'red'; // red, blue, green
    public string $cancelText = 'Cancel';
    public string $icon = 'danger'; // danger, warning, info, success

    protected $listeners = [
        'showConfirmModal' => 'showModal',
        'hideConfirmModal' => 'hideModal',
    ];

    public function showModal($config = [])
    {
        $this->title = $config['title'] ?? 'Confirm Action';
        $this->message = $config['message'] ?? 'Are you sure you want to proceed?';
        $this->confirmText = $config['confirmText'] ?? 'Confirm';
        $this->confirmColor = $config['confirmColor'] ?? 'red';
        $this->cancelText = $config['cancelText'] ?? 'Cancel';
        $this->icon = $config['icon'] ?? 'danger';

        $this->show = true;
    }

    public function hideModal()
    {
        $this->show = false;
    }

    public function confirm()
    {
        $this->dispatch('confirmed');
        $this->hideModal();
    }

    public function cancel()
    {
        $this->dispatch('cancelled');
        $this->hideModal();
    }

    public function render()
    {
        return view('livewire.components.confirm-modal');
    }
}
