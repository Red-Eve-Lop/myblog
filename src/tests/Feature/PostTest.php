<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private string $imageRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->imageRoot = sys_get_temp_dir().'/myblog-post-test-'.Str::uuid();
        config(['filesystems.disks.local.root' => $this->imageRoot]);
        Storage::forgetDisk('local');
    }

    protected function tearDown(): void
    {
        Storage::forgetDisk('local');
        File::deleteDirectory($this->imageRoot);
        parent::tearDown();
    }

    public function test_post_management_and_uploads_require_an_administrator(): void
    {
        $post = Post::create(['user_id' => User::factory()->create()->id, 'title' => '下書き']);
        $this->get('/admin/posts')->assertRedirect('/admin/login');
        $this->post('/admin/post-images', [])->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());
        $this->get('/admin/posts')->assertForbidden();
        $this->get('/admin/posts/create')->assertForbidden();
        $this->post('/admin/posts', [])->assertForbidden();
        $this->get("/admin/posts/{$post->id}/edit")->assertForbidden();
        $this->put("/admin/posts/{$post->id}", [])->assertForbidden();
        $this->post('/admin/post-images', [])->assertForbidden();
    }

    public function test_draft_publishing_editing_categories_and_private_images(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $this->get('/admin/posts/create')->assertOk()->assertSee('本文に画像を挿入')->assertSee('下書きで保存');
        $categories = [
            Category::create(['name' => '旅']),
            Category::create(['name' => '日記']),
        ];
        $bodyImage = $this->postJson('/admin/post-images', ['image' => UploadedFile::fake()->image('body.png')])
            ->assertCreated()->json();
        $featured = $this->postJson('/admin/post-images', ['image' => UploadedFile::fake()->image('cover.jpg')])
            ->assertCreated()->json();

        $data = [
            'title' => '旅の記事',
            'content' => '<h2>見出し</h2><p>本文<strong>太字</strong><script>alert(1)</script><a href="javascript:alert(1)">リンク</a></p><img src="'.$bodyImage['url'].'" alt="旅" onerror="alert(1)">',
            'category_ids' => array_map(fn ($category) => $category->id, $categories),
            'featured_image_id' => $featured['id'],
            'status' => 'draft',
        ];
        $this->post('/admin/posts', $data)->assertRedirect();
        $post = Post::where('title', '旅の記事')->firstOrFail();
        $this->get("/admin/posts/{$post->id}/edit")->assertOk()->assertSee('旅の記事')->assertSee('アイキャッチ画像');
        $this->assertSame('draft', $post->status);
        $this->assertCount(2, $post->categories);
        $this->assertStringContainsString($bodyImage['url'], $post->content);
        $this->assertStringNotContainsString('<script', $post->content);
        $this->assertStringNotContainsString('javascript:', $post->content);
        $this->assertStringNotContainsString('onerror', $post->content);
        $this->get($bodyImage['url'])->assertOk();
        $this->get('/admin/posts')->assertSee('下書き');

        auth()->logout();
        $this->get('/')->assertViewHas('posts', fn ($posts) => $posts->isEmpty());
        $this->get("/posts/{$post->id}")->assertNotFound();
        $this->get($bodyImage['url'])->assertNotFound();
        $this->get($featured['url'])->assertNotFound();

        $this->actingAs($admin);
        $data['status'] = 'published';
        $this->put("/admin/posts/{$post->id}", $data)->assertRedirect();
        $publishedAt = $post->fresh()->published_at;
        $this->assertNotNull($publishedAt);
        auth()->logout();
        $this->get('/')->assertViewHas('posts', fn ($posts) => $posts->contains('title', '旅の記事'));
        $this->get("/posts/{$post->id}")->assertOk()->assertSee('見出し')->assertSee($bodyImage['url'])->assertDontSee('alert(1)', false);
        $this->get($bodyImage['url'])->assertOk();
        $this->get($featured['url'])->assertOk();

        $this->actingAs($admin);
        $data['title'] = '変更後の記事';
        $data['category_ids'] = [$categories[1]->id];
        $this->put("/admin/posts/{$post->id}", $data)->assertRedirect();
        $this->assertTrue($publishedAt->equalTo($post->fresh()->published_at));
        $this->assertCount(1, $post->fresh()->categories);
        $data['status'] = 'draft';
        $this->put("/admin/posts/{$post->id}", $data)->assertRedirect();
        auth()->logout();
        $this->get('/')->assertViewHas('posts', fn ($posts) => $posts->isEmpty());
        $this->get("/posts/{$post->id}")->assertNotFound();
        $this->get($bodyImage['url'])->assertNotFound();
    }

    public function test_invalid_content_categories_and_images_cannot_be_published(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $base = ['title' => '記事', 'content' => '<p>本文</p>', 'status' => 'published'];
        $this->post('/admin/posts', [...$base, 'title' => ''])->assertSessionHasErrors('title');
        $this->post('/admin/posts', [...$base, 'content' => '<p></p>'])->assertSessionHasErrors('content');
        $this->post('/admin/posts', [...$base, 'category_ids' => [999]])->assertSessionHasErrors('category_ids.0');
        $this->post('/admin/posts', [...$base, 'content' => '<img src="/post-images/'.Str::uuid().'">'])->assertSessionHasErrors('content');
        $this->postJson('/admin/post-images', ['image' => UploadedFile::fake()->create('file.txt', 10, 'text/plain')])
            ->assertUnprocessable();
        $this->postJson('/admin/post-images', ['image' => UploadedFile::fake()->image('large.png')->size(2100)])
            ->assertUnprocessable();
        $this->assertSame(0, Post::count());
    }

    public function test_only_published_posts_offer_x_sharing(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::create([
            'user_id' => $admin->id,
            'title' => '旅 & "バイク"',
            'content' => '<p>本文</p>',
            'status' => 'published',
        ]);

        $response = $this->get("/posts/{$post->id}")->assertOk()->assertSee('Xでシェアする');
        $document = new \DOMDocument;
        @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $link = $document->getElementById('share-on-x');
        $this->assertNotNull($link);
        $this->assertSame($post->title, $link->getAttribute('data-title'));
        $this->assertSame("/posts/{$post->id}", $link->getAttribute('data-path'));

        $post->update(['status' => 'private']);
        $this->actingAs($admin)->get("/posts/{$post->id}")
            ->assertOk()->assertDontSee('Xでシェアする');
    }

    public function test_blank_paragraphs_are_preserved_when_saving_and_showing_a_post(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->post('/admin/posts', [
            'title' => '空行のある記事',
            'content' => '<p>前の段落</p><p></p><p>後の段落</p>',
            'status' => 'published',
        ])->assertRedirect();

        $post = Post::where('title', '空行のある記事')->firstOrFail();
        $this->assertStringContainsString('<p><br /></p>', $post->content);

        auth()->logout();
        $this->get("/posts/{$post->id}")
            ->assertOk()
            ->assertSee('<p><br /></p>', false);
    }

    public function test_private_posts_and_their_images_are_visible_only_to_administrators(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $image = $this->postJson('/admin/post-images', ['image' => UploadedFile::fake()->image('private.png')])
            ->assertCreated()->json();
        $data = [
            'title' => '限定記事',
            'content' => '<p>非公開の本文</p><img src="'.$image['url'].'">',
            'featured_image_id' => $image['id'],
            'status' => 'private',
        ];

        $this->post('/admin/posts', $data)->assertRedirect();
        $post = Post::where('title', '限定記事')->firstOrFail();
        $this->assertSame('private', $post->status);
        $this->get('/admin/posts')->assertSee('非公開');
        $this->get("/admin/posts/{$post->id}/edit")->assertSee('非公開で保存')->assertSee('非公開記事を表示');
        $response = $this->get("/posts/{$post->id}")->assertOk()
            ->assertSee('非公開の本文')->assertSee('管理者のみ閲覧できます')->assertSee('noindex');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->get($image['url'])->assertOk();
        $this->get('/')->assertViewHas('posts', fn ($posts) => $posts->isEmpty());

        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get("/posts/{$post->id}")->assertOk();
        $this->get($image['url'])->assertOk();

        $this->actingAs(User::factory()->create());
        $this->get("/posts/{$post->id}")->assertNotFound();
        $this->get($image['url'])->assertNotFound();
        auth()->logout();
        $this->get("/posts/{$post->id}")->assertNotFound();
        $this->get($image['url'])->assertNotFound();

        $this->actingAs($admin);
        $this->put("/admin/posts/{$post->id}", [...$data, 'status' => 'published'])->assertRedirect();
        auth()->logout();
        $this->get("/posts/{$post->id}")->assertOk();
        $this->get($image['url'])->assertOk();
        $this->get('/')->assertViewHas('posts', fn ($posts) => $posts->contains('title', '限定記事'));

        $this->actingAs($admin);
        $this->put("/admin/posts/{$post->id}", $data)->assertRedirect();
        $this->assertSame('private', $post->fresh()->status);
        auth()->logout();
        $this->get("/posts/{$post->id}")->assertNotFound();
        $this->get($image['url'])->assertNotFound();
        $this->get('/')->assertViewHas('posts', fn ($posts) => $posts->isEmpty());

        $this->actingAs($admin);
        $this->put("/admin/posts/{$post->id}", [...$data, 'status' => 'draft'])->assertRedirect();
        $this->get("/posts/{$post->id}")->assertNotFound();
    }

    public function test_removed_images_become_private_and_cannot_be_reused_by_another_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        $image = $this->postJson('/admin/post-images', ['image' => UploadedFile::fake()->image('photo.png')])->json();
        $data = ['title' => '記事', 'content' => '<p>本文</p><img src="'.$image['url'].'">', 'status' => 'published'];
        $this->post('/admin/posts', $data)->assertRedirect();
        $post = Post::firstOrFail();

        $this->post('/admin/posts', [...$data, 'title' => '別の記事'])->assertSessionHasErrors('content');
        $this->assertSame($post->id, PostImage::findOrFail($image['id'])->post_id);
        $this->put("/admin/posts/{$post->id}", [...$data, 'content' => '<p>画像なし</p>'])->assertRedirect();
        $this->assertNull(PostImage::findOrFail($image['id'])->post_id);
        auth()->logout();
        $this->get($image['url'])->assertNotFound();
    }
}
