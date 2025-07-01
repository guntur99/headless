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
                <h2 class="text-3xl font-bold text-gray-800 mb-3 sm:mb-0">{{ __('Media Manager Management') }}</h2>
                @can('create media_manager')
                <button wire:click="showCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-800 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ __('Add Image') }}
                </button>
                @endcan
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search name') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-gray-900">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 w-32">{{ __('Image') }}</th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" wire:click="sortBy('filename')">
                                <div class="flex items-center">
                                    {{ __('Filename') }}
                                    @if($sortField === 'filename')
                                    <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody wire:loading.class.delay="opacity-50">
                        @forelse ($images as $image)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <img src="{{ $image->path ?: 'https://placehold.co/100x100/e2e8f0/e2e8f0?text=.' }}"
                                    alt="Media Manager Image" class="w-20 h-20 object-cover rounded-md shadow-sm">
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $image->filename }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @can('edit media_manager')
                                <button wire:click="edit({{ $image->id }})"
                                    class="font-medium text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out">
                                    {{ __('Edit') }}
                                </button>
                                @endcan
                                @can('delete media_manager')
                                <button wire:click="delete({{ $image->id }})"
                                    wire:confirm="Are you sure you want to delete this image?"
                                    class="ml-4 font-medium text-red-600 hover:text-red-900 transition duration-150 ease-in-out">
                                    {{ __('Delete') }}
                                </button>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500">
                                {{ __('No image data found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $images->links() }}
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

                <div @click.away="open = false"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
                                        @if ($imageId)
                                            {{ __('Edit Image') }}
                                        @else
                                            {{ __('Add New Image') }}
                                        @endif
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">

                                        <div class="space-y-4">
                                            <div>
                                                <label for="filename"
                                                    class="block text-sm font-medium text-gray-700">{{ __('Filename') }}</label>
                                                <input type="text" wire:model.live="filename" id="filename"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">
                                                @error('filename') <span class="text-red-500 text-xs mt-1">{{ $message
                                                    }}</span> @enderror

                                                <div x-data="{ imagePreview: null }">
                                                    <label class="block mt-5 text-sm font-medium text-gray-700">{{ __('Image') }}</label>
                                                    <input type="file" wire:model="imageFile"
                                                        @change="imagePreview = URL.createObjectURL($event.target.files[0])"
                                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                    @error('imageFile') <span class="text-red-500 text-xs mt-1">{{ $message
                                                        }}</span> @enderror

                                                    <div wire:loading wire:target="imageFile"
                                                        class="text-sm text-gray-500 mt-2">{{ __('Uploading') }}...</div>

                                                    <div class="mt-4">
                                                        <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Preview') }}:</p>
                                                        <template x-if="imagePreview">
                                                            <img :src="imagePreview"
                                                                class="w-48 h-48 object-cover rounded-md shadow-sm">
                                                        </template>
                                                        <template x-if="!imagePreview && '{{ $path }}'">
                                                            <img src="{{ $path }}"
                                                                class="w-48 h-48 object-cover rounded-md shadow-sm">
                                                        </template>
                                                        <template x-if="!imagePreview && !'{{ $path }}'">
                                                            <div
                                                                class="w-48 h-48 bg-gray-100 rounded-md flex items-center justify-center text-gray-400">
                                                                {{ __('No picture.') }}
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            @can('create media_manager')
                            <button type="submit" wire:loading.attr="disabled"
                                class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                                <span wire:loading.remove wire:target="save">{{ __('Save') }}</span>
                                <span wire:loading wire:target="save">{{ __('Saving') }}...</span>
                            </button>
                            @endcan
                            <button type="button" @click="open = false"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                                {{ __('Cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
