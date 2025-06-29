<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\Category;
use App\Models\Page;

class Dashboard extends Component
{
    public $totalPosts;
    public $totalCategories;
    public $totalPages;
    public $recentPosts;

    public function mount()
    {
        $this->totalPosts = Post::count();
        $this->totalCategories = Category::count();
        $this->totalPages = Page::count();
        $this->recentPosts = Post::latest()->take(10)->get();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
