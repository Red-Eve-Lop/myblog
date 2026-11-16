<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        $userId = '137478304';

        $latestVideoId = Cache::remember("niconico_latest_video_{$userId}", 1800, function () use ($userId) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders([
                        'X-Frontend-Id' => '6',
                        'X-Frontend-Version' => '0',
                    ])
                    ->get("https://nvapi.nicovideo.jp/v3/users/{$userId}/videos", [
                        'selectContentType' => 'long',
                        'sortKey' => 'registeredAt',
                        'sortOrder' => 'desc',
                        'sensitiveContents' => 'mask',
                        'pageSize' => 1,
                        'page' => 1,
                    ]);
            } catch (ConnectionException $exception) {
                Log::warning('ニコニコ動画の最新動画を取得できませんでした。', ['error' => $exception->getMessage()]);

                return 'sm44503972';
            }

            $videoId = $response->json('data.items.0.essential.id');

            if (! $response->successful() || ! is_string($videoId) || ! preg_match('/\A(?:sm|nm|so)?[0-9]+\z/', $videoId)) {
                Log::warning('ニコニコ動画の最新動画を取得できませんでした。', ['status' => $response->status()]);

                return 'sm44503972';
            }

            return $videoId;
        });

        return view('about', compact('latestVideoId'));
    }
}
