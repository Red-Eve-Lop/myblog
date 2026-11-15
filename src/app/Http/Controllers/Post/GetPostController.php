<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Application\Post\GetPostQuery;
use App\Application\Post\GetPostUseCase;

/**
 * 投稿取得のコントローラー
 */
class GetPostController extends Controller
{
    public function __construct(
        private GetPostUseCase $useCase
    ) {}

    public function __invoke(int $id)
    {
        $query = new GetPostQuery($id);

        $post = $this->useCase->execute($query);

        // 投稿が見つからない場合は404を返す
        if (!$post) {
            return response()->json(['message' => '投稿が見つかりません'], 404);
        }

        return response()->json([
            'id' => $post->id,
            'title' => $post->title(),
            'content' => $post->content(),
        ]);
    }
}
