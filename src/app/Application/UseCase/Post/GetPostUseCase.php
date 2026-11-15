<?php

namespace App\Application\Post;

use App\Domain\Post\PostRepository;

/**
 * 投稿取得のユースケース
 */
class GetPostUseCase
{
    public function __construct(
        private PostRepository $posts
    ) {}

    // 投稿を取得する
    public function execute(GetPostQuery $query)
    {
        return $this->posts->find($query->id);
    }
}
