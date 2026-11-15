<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquentモデル: 投稿
 */
class EloquentPost extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    // リレーション: 投稿は複数のカテゴリを持つ
    public function categories()
    {
        return $this->belongsToMany(
            EloquentCategory::class,
            'post_category',
            'post_id',
            'category_id'
        );
    }
}
