
<div class="py-12">
    <div class=" container mx-auto p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
        <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
            {{-- Stat Cards --}}
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h2 class="text-lg font-semibold">{{ __('Total Posts') }}</h2>
                    <p class="mt-5 text-8xl font-bold text-blue-600">{{ $totalPosts }}</p>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h2 class="text-lg font-semibold">{{ __('Total Categories') }}</h2>
                    <p class="mt-5 text-8xl font-bold text-green-600">{{ $totalCategories }}</p>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h2 class="text-lg font-semibold">{{ __('Total Pages') }}</h2>
                    <p class="mt-5 text-8xl font-bold text-purple-600">{{ $totalPages }}</p>
                </div>
            </div>

            {{-- Recent Posts --}}
            <div
                class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 mt-4">
                <h2 class="text-lg font-semibold mb-4">10 {{ __('Latest Posts') }}</h2>
                <ul class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @forelse($recentPosts as $post)
                    <li class="py-2">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-800 text-dark">{{ $post->title }}
                                <div class="text-xs text-gray-500 mt-1">
                                    Slug: {{ $post->slug }}
                                </div>
                            </span>
                            <span class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </li>
                    @empty
                    <li class="py-2 text-gray-500">No posts found.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
