<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Auth;

class Index extends Component
{
    use WithPagination;

    public $userId, $title, $slug, $excerpt, $content, $image, $imageFile, $status = 'draft', $published_at;
    public $isModalOpen = false;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public $canCreateUsers;
    public $canDeleteUsers;

    public $allRoles;
    public $selectedUser;
    public $selectedRole = null;

    public function mount()
    {
        $this->canCreateUsers = Auth::user()->can('create users');
        $this->canDeleteUsers = Auth::user()->can('delete users');
        $this->allRoles = Role::all();
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy($this->sortField, $this->sortDirection)->paginate(5);

        return view('livewire.users.index', [
            'users' => $users,
        ])->layout('layouts.app');
    }

    public function edit($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->selectedRole = $this->selectedUser->roles->first() ? $this->selectedUser->roles->first()->name : null;
        $this->openModal();
    }

    public function updateUserRoles()
    {
        if ($this->selectedRole) {
            $this->selectedUser->syncRoles([$this->selectedRole]);
        }

        session()->flash('message', 'User role updated successfully.');
        $this->closeModal();
    }

    public function openModal() {
        $this->isModalOpen = true;
    }

    public function closeModal() {
        $this->isModalOpen = false;
        $this->reset('selectedUser', 'selectedRole');
    }

    private function refreshData()
    {
        $this->users = User::with('roles')->get();
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
        $this->authorize('create users');
        $this->validate([
            'role_id' => 'required|integer',
        ]);

        $userData = [
            'role_id'        => $this->role_id,
        ];

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($userData);
            session()->flash('message', 'User role successfully updated.');
        }

        $this->resetFields();
        $this->isModalOpen = false;
    }

    public function delete($id)
    {
        $this->authorize('delete users');
        $user = user::findOrFail($id);

        if ($user->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->image));
        }

        $user->delete();
        session()->flash('message', 'user successfully deleted.');
    }

    public function resetFields()
    {
        $this->reset(['userId', 'title', 'slug', 'excerpt', 'content', 'image', 'imageFile', 'published_at', 'selectedCategories']);
        $this->status = 'draft';
        $this->resetErrorBag();
    }
}

