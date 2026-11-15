<?php

namespace App\Domain\Comment;

/**
 * コメントドメインモデル
 */
class Comment
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $postId,
        private string $authorName,
        private string $content,
    ) {}

    // ゲッター
    public function authorName(): string
    {
        return $this->authorName;
    }

    // ゲッター
    public function content(): string
    {
        return $this->content;
    }
}
