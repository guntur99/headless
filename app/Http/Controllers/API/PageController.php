<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Post;

class PageController extends Controller
{

    protected $pageRepo;

    public function __construct(PageRepositoryInterface $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    public function index(Request $request)
    {
        return $this->pageRepo->all($request->input('query'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'nullable|string',
            'status' => 'in:active,inactive',
        ]);

        return response()->json($this->pageRepo->create($data), 201);
    }

    public function showById($id)
    {
        return $this->pageRepo->findById($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string',
            'body' => 'sometimes|required|string',
            'status' => 'in:active,inactive',
        ]);

        return $this->pageRepo->update($id, $data);
    }

    public function destroy($id)
    {
        $deleted = $this->pageRepo->delete($id);

        return response()->json([
            'message' => $deleted ? 'Page deleted successfully.' : 'Failed to delete page.',
            'status' => $deleted,
            'id' => $id,
        ]);
    }
}
