<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Post;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['uuid', 'name', 'slug'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    protected static function booted()
    {
        static::deleting(function ($category) {
            $category->posts()->detach();
        });
    }
}
