<?php

namespace App\Application\Post;

use App\Domain\Post\PostRepository;

/**
 * 投稿更新のユースケース
 */
class UpdatePostUseCase
{
    public function __construct(
        private PostRepository $posts
    ) {}

    public function execute(UpdatePostCommand $command)
    {
        // 既存の投稿を取得
        $post = $this->posts->find($command->id);

        // 投稿が見つからない場合は例外を投げる
        if (!$post) {
            throw new \Exception('投稿が見つかりません');
        }

        // Domainモデルのルールを使って更新
        $post->changeTitle($command->title);
        $post->changeContent($command->content);

        // カテゴリ更新
        $post->clearCategories();
        foreach ($command->categoryIds as $categoryId) {
            $post->addCategoryById($categoryId);
        }

        // 永続化
        return $this->posts->save($post);
    }
}
