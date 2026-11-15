<?php

namespace App\Application\Post;

use App\Domain\Post\Post;
use App\Domain\Post\PostRepository;

/**
 * 投稿作成のユースケース
 */
class CreatePostUseCase
{
    public function __construct(
        private PostRepository $posts
    ) {
    }

    /**
     * 投稿を作成する
     *
     * @param CreatePostCommand $command
     * @return Post
     */
    public function execute(CreatePostCommand $command): Post
    {
        // Domainモデルを生成（不変性を守る）
        $post = new Post(
            id: null,
            title: $command->title,
            content: $command->content,
            userId: $command->userId,
        );

        // カテゴリ付与（Domainのルールを使う）
        foreach ($command->categoryIds as $categoryId) {
            $post->addCategoryById($categoryId);
        }

        // 永続化（インターフェースを使う）
        return $this->posts->save($post);
    }
}
