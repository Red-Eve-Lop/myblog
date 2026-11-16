<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'status', 'published_at', 'featured_image_id', 'user_id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(PostImage::class, 'featured_image_id');
    }
}
