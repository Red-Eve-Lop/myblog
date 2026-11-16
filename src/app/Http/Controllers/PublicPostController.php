<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Support\PostContent;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicPostController extends Controller
{
    public function index(PostContent $content): View
    {
        $posts = Post::where('status', 'published')
            ->with(['categories', 'featuredImage'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (Post $post) use ($content) {
                $text = html_entity_decode(strip_tags($content->clean($post->content ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');

                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'description' => Str::limit(preg_replace('/\s+/u', ' ', trim($text)), 100),
                    'categories' => $post->categories->map->only(['id', 'name']),
                    'url' => route('posts.show', $post, false),
                    'image' => $post->featuredImage ? route('post-images.show', $post->featuredImage, false) : null,
                    'date' => $post->published_at?->format('Y.m.d'),
                ];
            });

        return view('welcome', [
            'categories' => Category::orderBy('id')->get(['id', 'name', 'sample_key']),
            'posts' => $posts,
        ]);
    }

    public function show(Post $post, PostContent $content): View
    {
        abort_unless($post->status === 'published', 404);
        $post->load(['categories', 'featuredImage']);

        return view('posts.show', ['post' => $post, 'content' => $content->clean($post->content ?? '')]);
    }
}
