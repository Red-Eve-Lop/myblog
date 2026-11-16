<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AboutTest extends TestCase
{
    public function test_the_about_page_shows_the_blog_owner_profile(): void
    {
        Http::fake([
            'nvapi.nicovideo.jp/*' => Http::response([
                'data' => ['items' => [['essential' => ['id' => 'sm47028104']]]],
            ]),
        ]);

        $this->withoutVite()
            ->get('/about')
            ->assertOk()
            ->assertSee('管理人について')
            ->assertSee('このブログを書いている人')
            ->assertSee('images/profile-avatar.jpg')
            ->assertSee('管理人のアカウント画像')
            ->assertSee('バイクとツーリング')
            ->assertSee('href="https://x.com/C4URSzhA4J55075" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('https://embed.nicovideo.jp/watch/sm47028104');

        $this->get('/about')->assertSee('https://embed.nicovideo.jp/watch/sm47028104');
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => str_starts_with($request->url(), 'https://nvapi.nicovideo.jp/v3/users/137478304/videos?')
            && $request->hasHeader('X-Frontend-Id', '6')
            && $request['sortKey'] === 'registeredAt'
            && $request['sortOrder'] === 'desc'
            && $request['pageSize'] === 1);
    }

    public function test_the_existing_video_is_shown_when_the_api_fails(): void
    {
        Http::fake(['nvapi.nicovideo.jp/*' => Http::response([], 503)]);
        Log::shouldReceive('warning')->once();

        $this->withoutVite()->get('/about')
            ->assertOk()
            ->assertSee('https://embed.nicovideo.jp/watch/sm44503972');
    }

    public function test_the_existing_video_is_shown_when_the_api_returns_an_invalid_id(): void
    {
        Http::fake([
            'nvapi.nicovideo.jp/*' => Http::response([
                'data' => ['items' => [['essential' => ['id' => '../invalid']]]],
            ]),
        ]);
        Log::shouldReceive('warning')->once();

        $this->withoutVite()->get('/about')
            ->assertOk()
            ->assertSee('https://embed.nicovideo.jp/watch/sm44503972')
            ->assertDontSee('../invalid');
    }

    public function test_the_existing_video_is_shown_when_the_api_cannot_be_reached(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection failed'));
        Log::shouldReceive('warning')->once();

        $this->withoutVite()->get('/about')
            ->assertOk()
            ->assertSee('https://embed.nicovideo.jp/watch/sm44503972');
    }
}
