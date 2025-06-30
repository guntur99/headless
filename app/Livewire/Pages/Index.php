<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $title, $slug, $body, $status = 'active', $pageId;
    public $isModalOpen = false;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }


    public function showCreateModal()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->authorize('create pages');
        $this->validate([
            'title' => 'required|string',
            'status' => 'required|in:active,inactive',
            'body' => 'required',
            'slug' => [
                'required',
                'string',
                Rule::unique('pages', 'slug')->ignore($this->pageId, 'id'),
            ],
        ]);

        if ($this->pageId) {
            $page = Page::findOrFail($this->pageId);
            $page->update([
                'title'        => $this->title,
                'slug'         => $this->slug,
                'body'         => $this->body,
                'status'       => $this->status,
            ]);
            session()->flash('message', 'Page successfully updated.');
        } else {
            Page::create([
                'uuid'         => Str::uuid(),
                'title'        => $this->title,
                'slug'         => Str::slug($this->title),
                'body'         => $this->body,
                'status'       => $this->status,
            ]);

            session()->flash('message', 'Page successfully created.');
        }

        $this->resetFields();
        $this->isModalOpen = false;
    }

    public function edit($id)
    {
        $this->authorize('edit pages');
        $page = Page::findOrFail($id);
        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->body = $page->body;
        $this->status = $page->status;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $this->authorize('delete pages');
        $page = Page::findOrFail($id);
        $page->delete();
        session()->flash('message', 'Page successfully deleted.');
    }

    public function resetFields()
    {
        $this->pageId = null;
        $this->title = '';
        $this->body = '';
        $this->status = '';
        $this->slug = '';

        $this->reset(['pageId', 'title', 'slug', 'body']);
        $this->status = 'active';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('view pages');
        $query = Page::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('body', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $pages = $query->orderBy($this->sortField, $this->sortDirection)->paginate(5);

        return view('livewire.pages.index', [
            'pages' => $pages,
        ])->layout('layouts.app');
    }
}
