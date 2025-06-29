<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Post;

class CategoryController extends Controller
{
    protected $categoryRepo;

    public function __construct(CategoryRepositoryInterface $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function index()
    {
        return $this->categoryRepo->all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'slug' => 'nullable|unique:categories',
        ]);

        return response()->json($this->categoryRepo->create($data), 201);
    }

    public function show(Category $category)
    {
        return $this->categoryRepo->show($category);
    }

    public function search(Request $request)
    {
        $keyword = $request->get('query', '');
        return $this->categoryRepo->searchByName($keyword);
    }

    public function showById($uuid)
    {
        return $this->categoryRepo->findById($uuid);
    }

    public function searchByName(string $keyword)
    {
        return Post::with('categories')
            ->where('title', 'ILIKE', "%{$keyword}%")
            ->latest()
            ->paginate(10);
    }

    public function findById(int $id)
    {
        return Post::with('categories')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $updated = $this->categoryRepo->update($id, $data);
        return response()->json($updated);
    }

    public function destroy($id)
    {
        $deleted = $this->categoryRepo->delete($id);

        if ($deleted) {
            return response()->json([
                'message' => 'Category deleted successfully.',
                'status' => true,
                'id' => $id,
            ]);
        }

        return response()->json([
            'message' => 'Failed to delete category.',
            'status' => false,
        ], 500);
    }

}
