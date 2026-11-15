<?php

use App\Http\Controllers\Post\CreatePostController;
use App\Http\Controllers\Post\GetPostController;

// 投稿作成API
Route::post('/posts', CreatePostController::class);

// 投稿取得API
Route::get('/posts/{id}', GetPostController::class);