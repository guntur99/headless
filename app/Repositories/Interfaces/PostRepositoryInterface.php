<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use App\Models\Post;

interface PostRepositoryInterface
{
    public function all($keyword);
    public function findById(string $uuid);
    public function create(array $data);
    public function update(string $uuid, array $data);
    public function delete(string $uuid);
}
