<?php

namespace App\Domain\Post;

use App\Domain\Category\Category;

/**
 * 投稿ドメインモデル
 */
class Post
{
    public function __construct(
        public readonly ?int $id,
        private string $title,
        private string $content,
        public readonly int $userId,
        private array $categories = [],
        private bool $locked = false,
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function categories(): array
    {
        return $this->categories;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function unlock(): void
    {
        $this->locked = false;
    }

    // カテゴリを付与する
    // ビジネスルール: 投稿は同じカテゴリを複数持つことはできない
    public function addCategory(Category $category): void
    {
        // 重複防止（ビジネスルール）
        foreach ($this->categories as $c) {
            if ($c->id === $category->id) {
                return;
            }
        }

        $this->categories[] = $category;
    }

    // タイトルをリネームする
    // ビジネスルール: 投稿は必ずタイトルを持つ必要がある
    public function renameTitle(string $newTitle)
    {
        if ($newTitle === '') {
            throw new DomainException('タイトルは空にできません');
        }
        $this->title = $newTitle;
    }

}
