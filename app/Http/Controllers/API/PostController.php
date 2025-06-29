<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    protected $postRepo;

    public function __construct(PostRepositoryInterface $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    public function index()
    {
        return $this->postRepo->all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'in:draft,published',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_ids' => 'required|min:1|array',
        ]);

        return response()->json($this->postRepo->create($data), 201);
    }

    public function show(Post $post)
    {
        return $this->postRepo->find($post);
    }

    public function search(Request $request)
    {
        $keyword = $request->get('query', '');
        return $this->postRepo->searchByName($keyword);
    }

    public function showById($id)
    {
        return $this->postRepo->findById($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string',
            'excerpt' => 'nullable|string',
            'content' => 'sometimes|required|string',
            'status' => 'in:draft,published',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'category_ids' => 'required|min:1|array',
        ]);

        return $this->postRepo->update($id, $data);
    }

    public function destroy($id)
    {
        $deleted = $this->postRepo->delete($id);

        return response()->json([
            'message' => $deleted ? 'Post deleted successfully.' : 'Failed to delete post.',
            'status' => $deleted,
            'id' => $id,
        ]);
    }
}
