<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\PostImageController as AdminPostImageController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\PublicPostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPostController::class, 'index']);
Route::get('/about', AboutController::class)->name('about');
Route::get('/posts/{post}', [PublicPostController::class, 'show'])->name('posts.show');
Route::get('/post-images/{image}', [PostImageController::class, 'show'])->name('post-images.show');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('admin.login');

    Route::middleware(['auth', 'can:access-admin'])->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('admin.logout');
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
        Route::get('/posts', [AdminPostController::class, 'index'])->name('admin.posts.index');
        Route::get('/posts/create', [AdminPostController::class, 'create'])->name('admin.posts.create');
        Route::post('/posts', [AdminPostController::class, 'store'])->name('admin.posts.store');
        Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('admin.posts.edit');
        Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('admin.posts.update');
        Route::post('/post-images', [AdminPostImageController::class, 'store'])->name('admin.post-images.store');
    });
});
