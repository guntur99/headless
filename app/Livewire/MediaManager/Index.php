<?php

namespace App\Livewire\MediaManager;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\MediaManager;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public $filename, $imageId, $path, $imageFile;
    public $isModalOpen = false;

    public $media = [];
    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function showCreateModal()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->authorize('create media_manager');
        $this->validate([
            'filename' => [
                'required',
                'string',
                Rule::unique('media_managers', 'filename')->ignore($this->imageId, 'id'),
            ],
            'imageFile' => 'nullable|image|max:2048',
        ]);

        $imageUrl = $this->path;

        if ($this->imageFile) {
            if ($this->imageId && $this->path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $this->image));
            }
            $path = $this->imageFile->store('media', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        }

        $mediaData = [
            'filename' => $this->filename,
            'path'     => $imageUrl,
        ];

        if ($this->imageId) {
            $media = MediaManager::findOrFail($this->imageId);
            $media->update($mediaData);
            session()->flash('message', 'Image successfully updated.');
        } else {
            $mediaData['uuid'] = Str::uuid();
            $media = MediaManager::create($mediaData);
            session()->flash('message', 'Image successfully created.');
        }

        $this->resetFields();
        $this->isModalOpen = false;
    }

    public function edit($id)
    {
        $this->authorize('edit media_manager');
        $image = MediaManager::findOrFail($id);
        $this->imageId = $image->id;
        $this->filename = $image->filename;
        $this->path = $image->path;
        $this->imageFile = null;
        $this->isModalOpen = true;
    }

    public function delete($id)
    {
        $this->authorize('delete media_manager');
        $image = MediaManager::findOrFail($id);

        if ($image->path) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image->path));
        }
        $image->delete();
        session()->flash('message', 'Post successfully deleted.');
    }

    public function resetFields()
    {
        $this->reset(['imageId', 'filename', 'path', 'imageFile']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('view media_manager');

        $query = MediaManager::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('filename', 'like', '%' . $this->search . '%');
            });
        }

        $images = $query->orderBy($this->sortField, $this->sortDirection)->paginate(5);

        return view('livewire.media-manager.index', [
            'images' => $images,
        ])->layout('layouts.app');
    }
}
