<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Support\PostContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', ['posts' => Post::orderByDesc('updated_at')->get()]);
    }

    public function create(): View
    {
        return view('admin.posts.form', ['post' => new Post, 'categories' => Category::orderBy('id')->get()]);
    }

    public function edit(Post $post): View
    {
        $post->load('categories', 'featuredImage');

        return view('admin.posts.form', ['post' => $post, 'categories' => Category::orderBy('id')->get()]);
    }

    public function store(Request $request, PostContent $content): RedirectResponse
    {
        $post = new Post;
        $post->user_id = $request->user()->id;
        $this->save($request, $post, $content);

        return redirect()->route('admin.posts.edit', $post)->with('status', '記事を保存しました。');
    }

    public function update(Request $request, Post $post, PostContent $content): RedirectResponse
    {
        $this->save($request, $post, $content);

        return redirect()->route('admin.posts.edit', $post)->with('status', '記事を保存しました。');
    }

    private function save(Request $request, Post $post, PostContent $content): void
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', Rule::in(['draft', 'private', 'published'])],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', Rule::exists('categories', 'id')],
            'featured_image_id' => ['nullable', 'uuid'],
        ], [
            'title.required' => 'タイトルを入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'content.max' => '本文は10,000文字以内で入力してください。',
            'category_ids.*.exists' => '選択したカテゴリーが存在しません。',
        ]);

        $html = $content->clean($data['content'] ?? '');
        $imageIds = $content->imageIds($html);
        if ($data['status'] === 'published' && trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')) === '' && $imageIds === []) {
            throw ValidationException::withMessages(['content' => '公開するには本文を入力してください。']);
        }

        if (! empty($data['featured_image_id'])) {
            $imageIds[] = strtolower($data['featured_image_id']);
        }
        $imageIds = array_values(array_unique($imageIds));

        DB::transaction(function () use ($request, $post, $data, $html, $imageIds) {
            if ($post->exists) {
                $current = Post::whereKey($post->id)->lockForUpdate()->firstOrFail();
                $post->setRawAttributes($current->getAttributes(), true);
            }

            $images = PostImage::whereIn('id', $imageIds)->lockForUpdate()->get();
            if ($images->count() !== count($imageIds) || $images->contains(function (PostImage $image) use ($post, $request) {
                return $image->post_id !== null
                    ? $image->post_id !== $post->id
                    : $image->user_id !== $request->user()->id;
            })) {
                throw ValidationException::withMessages(['content' => '選択した画像をこの記事に使用できません。']);
            }

            $wasPublished = $post->status === 'published';
            $post->title = $data['title'];
            $post->content = $html;
            $post->status = $data['status'];
            $post->published_at = $data['status'] === 'published'
                ? ($wasPublished ? $post->published_at : now())
                : null;
            $post->featured_image_id = $data['featured_image_id'] ?? null;
            $post->save();
            $post->categories()->sync($data['category_ids'] ?? []);
            $post->images()->whereNotIn('id', $imageIds)->update(['post_id' => null]);
            PostImage::whereIn('id', $imageIds)->update(['post_id' => $post->id]);
        });
    }
}
