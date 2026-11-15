<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Post\PostRepository;
use App\Infrastructure\Persistence\Repository\EloquentPostRepository;

/**
 * リポジトリサービスプロバイダー
 */
class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * PostRepositoryのサービスプロバイダー
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            PostRepository::class,
            EloquentPostRepository::class
        );
    }
}
