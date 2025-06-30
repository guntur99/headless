<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Index extends Component
{
    use WithPagination;

    public $roleId, $name;
    public $permissionGroups;
    public $selectedPermissions = [];
    public $isModalOpen = false;

    public $search = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $allPermissionsCount;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->allPermissionsCount = Permission::count();
        $this->initializePermissions();
    }

    private function initializePermissions()
    {
        $modules = ['posts', 'pages', 'categories'];
        $actions = ['view', 'create', 'edit', 'delete'];

        $this->permissionGroups = collect($modules)->map(function ($module) use ($actions) {
            return [
                'name' => ucfirst($module),
                'permissions' => collect($actions)->map(function ($action) use ($module) {
                    return "{$action} {$module}";
                })->all()
            ];
        })->all();
    }

    public function render()
    {
        $query = Role::with('permissions');
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }
        $roles = $query->orderBy($this->sortField, $this->sortDirection)->paginate(5);

        return view('livewire.roles.index', [
            'roles' => $roles,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->openModal();
    }

    public function store()
    {
        $this->validate(['name' => 'required|min:3']);
        $role = Role::updateOrCreate(['id' => $this->roleId], ['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);
        session()->flash('message', $this->roleId ? 'Role updated successfully.' : 'Role created successfully.');
        $this->closeModal();
        $this->resetPage();
    }

    public function delete($id)
    {
        Role::find($id)->delete();
        session()->flash('message', 'Role deleted successfully.');
        $this->resetPage();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    private function resetForm()
    {
        $this->roleId = null;
        $this->name = '';
        $this->selectedPermissions = [];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
