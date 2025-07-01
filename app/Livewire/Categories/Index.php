<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $name, $slug, $categoryId;
    public $isModalOpen = false;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function showCreateModal()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->authorize('create categories');
        $this->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('categories', 'name')->ignore($this->categoryId, 'id'),
            ],
            'slug' => [
                'required',
                'string',
                Rule::unique('categories', 'slug')->ignore($this->categoryId, 'id'),
            ],
        ]);

        Category::updateOrCreate(
            ['id' => $this->categoryId],
            ['name' => $this->name, 'uuid' => (string) Str::uuid(), 'slug' => $this->slug]
        );

        $this->resetFields();
        $this->categories = Category::latest()->get();
        $this->isModalOpen = false;
    }

    public function edit($id)
    {
        $this->authorize('edit categories');
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $this->authorize('delete categories');
        $category = Category::findOrFail($id);
        $category->delete();
        $this->categories = Category::latest()->get();
    }

    public function resetFields()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->slug = '';
    }

    public function render()
    {
        $this->authorize('view categories');
        $query = Category::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%']);
            });
        }

        $categories = $query->orderBy($this->sortField, $this->sortDirection)->paginate(5);

        return view('livewire.categories.index', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
