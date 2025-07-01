<?php

namespace App\Repositories;

use App\Models\Page;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class PageRepository implements PageRepositoryInterface
{
    public function all($keyword)
    {
        return Page::when($keyword, function($query) use($keyword) {
                $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($keyword) . '%']);
            })
            ->latest()
            ->paginate(10);
    }

    public function findById(string $uuid)
    {
        return Page::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $data['uuid'] = Str::uuid();

        $page = Page::create($data);
        return $page;
    }

    public function update(string $uuid, array $data)
    {
        $page = Page::where('uuid', $uuid)->firstOrFail();

        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $page->update($data);
        return $page->refresh();
    }

    public function delete($uuid)
    {
        $page = Page::where('uuid', $uuid)->firstOrFail();
        return $page->delete();
    }

}
