<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_category_management_requires_an_administrator(): void
    {
        $category = Category::create(['name' => '旅行']);

        $this->get('/admin/categories')->assertRedirect('/admin/login');
        $this->post('/admin/categories', ['name' => '新しいカテゴリー'])->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());
        $this->get('/admin/categories')->assertForbidden();
        $this->post('/admin/categories', ['name' => '新しいカテゴリー'])->assertForbidden();
        $this->get("/admin/categories/{$category->id}/edit")->assertForbidden();
        $this->put("/admin/categories/{$category->id}", ['name' => '変更'])->assertForbidden();
        $this->delete("/admin/categories/{$category->id}")->assertForbidden();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => '旅行']);
        $this->assertDatabaseMissing('categories', ['name' => '新しいカテゴリー']);
    }

    public function test_administrator_can_create_rename_and_delete_an_unused_category(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->post('/admin/categories', ['name' => '旅行'])->assertRedirect('/admin/categories');
        $category = Category::where('name', '旅行')->firstOrFail();
        $this->get('/admin/categories')->assertOk()->assertSee('旅行');
        $this->get("/admin/categories/{$category->id}/edit")->assertOk()->assertSee('旅行');

        $this->put("/admin/categories/{$category->id}", ['name' => '旅の記録'])
            ->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => '旅の記録']);
        $this->get('/')->assertViewHas('categories', fn ($categories) => $categories->contains('name', '旅の記録'));

        $this->delete("/admin/categories/{$category->id}")->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_names_must_be_valid_and_unique(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $category = Category::create(['name' => '旅行']);
        $other = Category::create(['name' => '日記']);

        foreach (['', 'すべて', '旅行', str_repeat('a', 256)] as $name) {
            $this->post('/admin/categories', ['name' => $name])->assertSessionHasErrors('name');
        }
        $this->put("/admin/categories/{$other->id}", ['name' => '旅行'])->assertSessionHasErrors('name');
        $this->put("/admin/categories/{$category->id}", ['name' => '旅行'])->assertSessionHasNoErrors();
        $this->assertSame(2, Category::count());
    }

    public function test_seeder_reuses_existing_categories_and_preserves_renames(): void
    {
        $existing = Category::create(['name' => 'ツーリング']);
        $this->seed(CategorySeeder::class);
        $this->assertSame('touring', $existing->fresh()->sample_key);

        $existing->update(['name' => '旅の記録']);
        $this->seed(CategorySeeder::class);
        $this->assertSame(3, Category::count());
        $this->get('/')->assertViewHas('categories', function ($categories) use ($existing) {
            return $categories->contains(fn ($category) => $category->id === $existing->id
                && $category->sample_key === 'touring'
                && $category->name === '旅の記録');
        });
    }

    public function test_categories_used_by_posts_or_samples_cannot_be_deleted(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);
        $this->seed(CategorySeeder::class);
        $sample = Category::where('sample_key', 'touring')->firstOrFail();
        $category = Category::create(['name' => '旅行']);
        $postId = DB::table('posts')->insertGetId([
            'user_id' => $user->id,
            'title' => '記事',
            'content' => '本文',
        ]);
        DB::table('post_category')->insert(['post_id' => $postId, 'category_id' => $category->id]);

        foreach ([$sample, $category] as $used) {
            $this->delete("/admin/categories/{$used->id}")->assertSessionHasErrors('category');
            $this->assertDatabaseHas('categories', ['id' => $used->id]);
        }
        $this->assertDatabaseHas('post_category', ['post_id' => $postId, 'category_id' => $category->id]);
    }
}
