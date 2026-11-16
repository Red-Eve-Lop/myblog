<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use RuntimeException;

class PostImageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', File::image()->types(['jpg', 'jpeg', 'png', 'gif', 'webp'])->max('2mb')],
        ]);

        $file = $request->file('image');
        $path = $file->store('post-images', 'local');
        if ($path === false) {
            throw new RuntimeException('画像を保存できませんでした。');
        }

        $image = PostImage::create([
            'user_id' => $request->user()->id,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json(['id' => $image->id, 'url' => route('post-images.show', $image, false)], 201);
    }
}
