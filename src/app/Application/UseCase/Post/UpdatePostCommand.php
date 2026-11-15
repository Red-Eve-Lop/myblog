<?php

namespace App\Application\Post;

/**
 * 投稿更新のDTO
 */
class UpdatePostCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly array $categoryIds = [],
    ) {}
}
