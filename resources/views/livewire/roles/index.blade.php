<div class="py-12">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">

        @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">{{ __('Successfully!') }}</strong>
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-lg">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-3 sm:mb-0">{{ __('Roles Management') }}</h2>
                <button wire:click="showCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ __('Add Roles') }}
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Users Management') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-900">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('name')">
                                <div class="flex items-center">
                                    {{ __('Name') }}
                                    @if($sortField === 'name')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3">{{ __('Permissions') }}</th>
                            <th scope="col" class="px-6 py-3 text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class.delay="opacity-50">
                        @forelse ($roles as $role)
                        @php
                            $rolePermissionsCount = $role->permissions->count();
                            $displayPermissions = $role->permissions->sortBy('name')->take(7);
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $role->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($rolePermissionsCount >= $allPermissionsCount)
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-purple-800 bg-purple-100 rounded-full">
                                        {{ __('All Permissions') }}
                                    </span>
                                @elseif($rolePermissionsCount > 7)
                                    @foreach($displayPermissions as $permission)
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full mr-1 mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                    <span class="inline-flex px-2 text-xs font-semibold leading-5 text-gray-800 bg-gray-100 rounded-full">+{{ $rolePermissionsCount - 5 }} more</span>
                                @else
                                    @forelse($role->permissions->sortBy('name') as $permission)
                                        <span class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full mr-1 mb-1">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-xs text-gray-500">No permissions assigned</span>
                                    @endforelse
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <button wire:click="edit({{ $role->id }})" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</button>
                                <button wire:click="delete({{ $role->id }})" wire:confirm="Are you sure you want to delete this role?"
                                    class="ml-4 text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                {{ __('No role data found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $roles->links() }}
            </div>
        </div>


        @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-data @click.self="window.livewire.emit('closeModal')">
            <div class="p-8 mx-4 bg-white rounded-lg shadow-xl md:max-w-2xl md:mx-auto">
                <h2 class="text-2xl font-bold">{{ $roleId ? 'Edit Role' : 'Create Role' }}</h2>
                <form wire:submit.prevent="store" class="mt-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Role Name') }}</label>
                        <input type="text" wire:model.defer="name" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Permissions') }}</h3>
                        <div class="mt-4 overflow-hidden border border-gray-200 rounded-md">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">{{ __('Module') }}</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">{{ __('View') }}</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">{{ __('Create') }}</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">{{ __('Edit') }}</th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">{{ __('Delete') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($permissionGroups as $group)
                                    <tr>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ __($group['name']) }}</td>
                                        @foreach($group['permissions'] as $permission)
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                <input type="checkbox" wire:model.defer="selectedPermissions" value="{{ $permission }}" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pt-5 mt-6 border-t border-gray-200">
                        <div class="flex justify-end">
                            <button type="button" wire:click="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="inline-flex justify-center px-4 py-2 ml-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
