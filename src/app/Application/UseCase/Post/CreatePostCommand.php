<?php

namespace App\Application\Post;

/**
 * 投稿作成のDTO(データ渡すようのオブジェクトをそう呼ぶんだって)
 */
class CreatePostCommand
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly int $userId,
        public readonly array $categoryIds = [],
    ) {}
}
