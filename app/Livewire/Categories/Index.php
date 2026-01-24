<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\On;

class Index extends Component
{
    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $categoryToEdit = null;
    public $categoryToDelete = null;

    protected $listeners = [
        'confirmed' => 'handleConfirmed',
        'cancelled' => 'handleCancelled',
    ];

    public function mount(){
        if (request()->query('action') === 'create-category') {
            $this->dispatch('open-modal', 'create-category');
        }
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->dispatch('open-modal', 'create-category');
    }

    public function openEditModal($categoryId)
    {
        $this->categoryToEdit = $categoryId;
        $this->dispatch('open-modal', 'edit-category');
    }

    #[On('category-created')]
    #[On('category-updated')]
    #[On('category-deleted')]
    public function refreshCategories()
    {

    }

    public function confirmDelete($categoryId)
    {
        $category = Category::findorFail($categoryId);
        $this->categoryToDelete = $categoryId;

        $this->dispatch('showConfirmModal', [
            'title' => 'Delete Category',
            'message' => "Are you sure you want to delete '{$category->name}'? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmColor' => 'red',
            'icon' => 'danger',
        ]);
    }

    public function handleConfirmed()
    {
        try {
            $category = Category::findOrFail($this->categoryToDelete);

            // Check if category has products
            if ($category->products()->count() > 0) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Cannot delete category with existing products. Please reassign or delete products first.'
                ]);
                return;
            }

            $category->delete();

            $this->dispatch('category-deleted');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Failed to delete category. Please try again.'
            ]);
        }
    }

    public function handleCancelled()
    {
        $this->categoryToDelete = null;
    }

    public function getCategoriesProperty()
    {
        return Category::query()
            ->withCount('products')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.categories.index', [
            'categories' => $this->categories
        ]);
    }
}
