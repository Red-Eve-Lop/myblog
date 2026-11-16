<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutTest extends TestCase
{
    public function test_the_about_page_shows_the_blog_owner_profile(): void
    {
        $this->withoutVite()
            ->get('/about')
            ->assertOk()
            ->assertSee('管理人について')
            ->assertSee('このブログを書いている人')
            ->assertSee('バイクとツーリング');
    }
}
