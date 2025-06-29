<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class PostRepository implements PostRepositoryInterface
{
    public function all()
    {
        return Post::with('categories')->latest()->paginate(10);
    }

    public function find(Post $post)
    {
        return $post->load('categories');
    }

    public function searchByName($keyword)
    {
        return Post::when($keyword, function($query) use($keyword) {
                $query->where('title', 'ILIKE', "%{$keyword}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function findById(string $uuid)
    {
        return Post::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['uuid'] = Str::uuid();
        $data['published_at'] = ($data['status'] ?? 'draft') === 'published' ? now() : null;

        if ($data['image']) {
            $path = $data['image']->store('posts', 'public'); // ganti 'public' ke 's3' jika perlu
            $data['image'] = Storage::disk('public')->url($path);
        }

        $post = Post::create($data);
        if (!empty($data['category_ids'])) {
            $post->categories()->sync($data['category_ids']);
        }

        return $post;
    }

    public function update(string $uuid, array $data)
    {
        $post = Post::where('uuid', $uuid)->firstOrFail();

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($data['image']) {
            $this->deleteImageIfExists($post->image);
            $path = $data['image']->store('posts', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        } else {
            $data['image'] = $post->image;
        }

        $data['published_at'] = ($data['status'] ?? $post->status) === 'published' ? now() : null;

        $post->update($data);

        // Sync categories if available
        if (isset($data['category_ids'])) {
            $post->categories()->sync($data['category_ids']);
        }

        return $post->refresh();
    }

    public function delete($uuid)
    {
        $post = Post::where('uuid', $uuid)->firstOrFail();
        $post->categories()->detach();

        if ($post->image) {
            $this->deleteImageIfExists($post->image);
        }

        return $post->delete();
    }

    protected function deleteImageIfExists($url)
    {
        $path = str_replace(Storage::disk('public')->url(''), '', $url);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
