<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Application\Post\CreatePostCommand;
use App\Application\Post\CreatePostUseCase;
use Illuminate\Http\Request;

/**
 * 投稿作成のコントローラー
 */
class CreatePostController extends Controller
{
    public function __construct(
        private CreatePostUseCase $useCase
    ) {}

    public function __invoke(Request $request)
    {
        $command = new CreatePostCommand(
            title: $request->input('title'),
            content: $request->input('content'),
            userId: $request->user()->id,
            categoryIds: $request->input('category_ids', []),
        );

        $post = $this->useCase->execute($command);

        return response()->json([
            'id' => $post->id,
            'title' => $post->title(),
            'content' => $post->content(),
        ]);
    }
}
