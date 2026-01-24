<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;

class Edit extends Component
{
    public $categoryId;
    public $name = '';
    public $description = '';
    public $icon = '';
    public $color = '#3B82F6';

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

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:categories,name,' . $this->categoryId,
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string',
            'color' => 'required|string',
        ];
    }

    protected $messages = [
        'name.required' => 'Category name is required.',
        'name.unique' => 'A category with this name already exists.',
        'icon.required' => 'Please select an icon.',
        'color.required' => 'Please select a color.',
    ];

    public function mount($categoryId)
    {
        $this->categoryId = $categoryId;
        $this->loadCategory();
    }

    public function loadCategory()
    {
        $category = Category::findOrFail($this->categoryId);

        $this->name = $category->name;
        $this->description = $category->description;
        $this->icon = $category->icon;
        $this->color = $category->color;
    }

    public function selectColor($color)
    {
        $this->color = $color;
    }

    public function update()
    {
        $this->validate();

        try {
            $category = Category::findOrFail($this->categoryId);

            $category->update([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon,
                'color' => $this->color,
            ]);

            $this->dispatch('close-modal', 'edit-category');
            $this->dispatch('category-updated');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Category updated successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to update category. Please try again.'
            ]);
        }
    }

    public function cancel()
    {
        $this->resetValidation();
        $this->dispatch('close-modal', 'edit-category');
    }

    public function render()
    {
        return view('livewire.categories.edit');
    }
}
