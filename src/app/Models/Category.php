<?php

namespace App\Models;

use App\Infrastructure\Persistence\Eloquent\EloquentPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['name'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(EloquentPost::class, 'post_category', 'category_id', 'post_id');
    }
}
