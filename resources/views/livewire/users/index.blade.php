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
                <h2 class="text-3xl font-bold text-gray-800 mb-3 sm:mb-0">{{ __('Users Management') }}</h2>
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
                            <th scope="col" class="px-6 py-3">{{ __('Email') }}</th>
                            <th scope="col" class="px-6 py-3">{{ __('Role') }}</th>
                            <th scope="col" class="px-6 py-3 text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class.delay="opacity-50">
                        @forelse ($users as $user)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full  bg-blue-100 text-blue-800'">
                                    @foreach($user->roles as $role)
                                    <span
                                        class="inline-flex px-2 text-xs font-semibold leading-5 text-blue-800 bg-blue-100 rounded-full">{{
                                        $role->name }}
                                    </span>
                                    @endforeach
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if (auth()->user()->name !== $user->name)
                                    <button wire:click="edit({{ $user->id }})"
                                        class="font-medium text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out">
                                        {{ __('Manage Roles') }}
                                    </button>
                                @else
                                <span
                                class="px-2 inline-flex text-xs leading-5 font-bold rounded-full  bg-red-100 text-red-500">
                                    {{ __('This is you') }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                {{ __('No user data found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>


        <div x-data="{ open: @entangle('isModalOpen').live }" x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                @if($isModalOpen && $selectedUser)
                <div @click.away="open = false"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="updateUserRoles">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
                                        {{ __('Manage Roles for') }} {{ $selectedUser->name }}
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                                        <select id="role-select"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            wire:model.defer="selectedRole" x-data="{}" x-init="
                                                $('#role-select').select2({
                                                    placeholder: 'Select a role',
                                                    allowClear: true
                                                });

                                                $('#role-select').on('change', function () {
                                                    $wire.set('selectedRole', $(this).val());
                                                });

                                                $wire.on('roleSelected', selectedValue => {
                                                    $('#role-select').val(selectedValue).trigger('change');
                                                });

                                                $wire.on('closeModal', () => {
                                                    $('#role-select').val(null).trigger('change');
                                                });
                                                                    ">
                                            <option value=""></option>
                                            @foreach($allRoles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                                <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving') }}...</span>
                            </button>
                            <button type="button" @click="open = false"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                    </form>
                </div>

                @endif
            </div>
        </div>
    </div>
</div>
