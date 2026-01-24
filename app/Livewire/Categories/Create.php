<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $description = '';
    public $icon = '';
    public $color = '#3B82F6'; // Default blue

    public $icons = [
        'laptop' => 'Electronics',
        'shirt' => 'Clothing & Apparel',
        'utensils' => 'Food & Beverages',
        'briefcase' => 'Office Supplies',
        'home' => 'Home & Garden',
        'heart' => 'Health & Beauty',
        'car' => 'Automotive',
        'gamepad' => 'Gaming',
        'tag' => 'Other'
    ];

    public $colors = [
        '#3B82F6', // Blue
        '#10B981', // Green
        '#EF4444', // Red
        '#8B5CF6', // Purple
        '#F97316', // Orange
        '#06B6D4', // Cyan
        '#EC4899', // Pink
        '#F59E0B', // Amber
    ];

    protected $rules = [
        'name' => 'required|string|max:100|unique:categories,name',
        'description' => 'nullable|string|max:500',
        'icon' => 'required|string',
        'color' => 'required|string',
    ];

    protected $messages = [
        'name.required' => 'Category name is required.',
        'name.unique' => 'A category with this name already exists.',
        'icon.required' => 'Please select an icon.',
        'color.required' => 'Please select a color.',
    ];

    public function mount()
    {
        $this->color = $this->colors[0];
    }

    public function selectColor($color)
    {
        $this->color = $color;
    }

    public function save()
    {
        $this->validate();

        try {
            Category::create([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon,
                'color' => $this->color,
            ]);

            $this->reset(['name', 'description', 'icon']);
            $this->color = $this->colors[0];

            $this->dispatch('close-modal', 'create-category');
            $this->dispatch('category-created');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Category created successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to create category. Please try again.'
            ]);
        }
    }

    public function cancel()
    {
        $this->reset(['name', 'description', 'icon']);
        $this->color = $this->colors[0];
        $this->resetValidation();
        $this->dispatch('close-modal', 'create-category');
    }

    public function render()
    {
        return view('livewire.categories.create');
    }
}
