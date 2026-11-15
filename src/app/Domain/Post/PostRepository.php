<?php

namespace App\Domain\Post;

/**
 * Postリポジトリインターフェース
 */
interface PostRepository
{
    public function save(Post $post): Post;

    public function find(int $id): ?Post;

    public function delete(Post $post): void;
}
