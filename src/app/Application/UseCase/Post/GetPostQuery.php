<?php

namespace App\Application\Post;

/**
 *  投稿取得のDTO
 */
class GetPostQuery
{
    public function __construct(
        public readonly int $id
    ) {}
}
