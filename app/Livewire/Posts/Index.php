<?php

namespace App\Livewire\Posts;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public $postId, $title, $slug, $excerpt, $content, $image, $imageFile, $status = 'draft', $published_at;
    public $isModalOpen = false;
    public $categories = [];
    public $selectedCategories = [];

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->authorize('view posts');
        $this->categories = Category::all();
    }

    public function render()
    {
        $query = Post::with('categories');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $posts = $query->orderBy($this->sortField, $this->sortDirection)->paginate(5);

        return view('livewire.posts.index', [
            'posts' => $posts,
        ])->layout('layouts.app');
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

    public function updatedTitle($value)
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
        $this->authorize('create posts');
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'imageFile' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
            'selectedCategories' => 'required|array|min:1',
            'selectedCategories.*' => 'exists:categories,id',
            'slug' => [
                'required',
                'string',
                Rule::unique('posts', 'slug')->ignore($this->postId),
            ],
        ]);

        $imageUrl = $this->image;

        if ($this->imageFile) {
            if ($this->postId && $this->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $this->image));
            }
            $path = $this->imageFile->store('posts', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        }

        $postData = [
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt,
            'content'      => $this->content,
            'image'        => $imageUrl,
            'status'       => $this->status,
            'published_at' => $this->status === 'published' ? Carbon::now() : null,
        ];

        if ($this->postId) {
            $post = Post::findOrFail($this->postId);
            $post->update($postData);
            session()->flash('message', 'Post successfully updated.');
        } else {
            $postData['uuid'] = Str::uuid();
            $post = Post::create($postData);
            session()->flash('message', 'Post successfully created.');
        }

        // Sync categories
        $post->categories()->sync($this->selectedCategories);

        $this->resetFields();
        $this->isModalOpen = false;
    }

    public function edit($id)
    {
        $this->authorize('edit posts');
        $post = Post::with('categories')->findOrFail($id);
        $this->postId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt;
        $this->content = $post->content;
        $this->image = $post->image;
        $this->imageFile = null;
        $this->status = $post->status;
        $this->published_at = $post->published_at;
        $this->selectedCategories = $post->categories->pluck('id')->toArray();
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $this->authorize('delete posts');
        $post = Post::findOrFail($id);

        if ($post->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $post->image));
        }

        $post->delete();
        session()->flash('message', 'Post successfully deleted.');
    }

    public function resetFields()
    {
        $this->reset(['postId', 'title', 'slug', 'excerpt', 'content', 'image', 'imageFile', 'published_at', 'selectedCategories']);
        $this->status = 'draft';
        $this->resetErrorBag();
    }
}

