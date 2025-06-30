<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Posts\Index as PostIndex;
use App\Livewire\Categories\Index as CategoryIndex;
use App\Livewire\Pages\Index as PageIndex;
use App\Livewire\Users\Index as UserIndex;
use App\Livewire\Roles\Index as RoleIndex;
use App\Livewire\Dashboard;

Route::redirect('/', 'login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('posts', PostIndex::class)->name('posts');
    Route::get('pages', PageIndex::class)->name('pages');
    Route::get('categories', CategoryIndex::class)->name('categories');
    Route::get('users', UserIndex::class)->middleware(['role:Super Admin'])->name('users');
    Route::get('roles', RoleIndex::class)->middleware(['role:Super Admin'])->name('roles');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
