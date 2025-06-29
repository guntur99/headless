<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::latest()->paginate(10);
    }

    public function find(Category $category)
    {
        return $category;
    }

    public function searchByName($keyword)
    {
        return Category::when($keyword, function($query) use($keyword) {
                $query->where('name', 'ILIKE', "%{$keyword}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function findById(string $uuid)
    {
        return Category::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['uuid'] = Str::uuid();
        return Category::create($data);
    }

    public function update(string $uuid, array $data)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $data['slug'] = Str::slug($data['name']);
        $category->update($data);
        return $category;
    }

    public function delete(string $uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->posts()->detach();
        return $category->delete();
    }

}
