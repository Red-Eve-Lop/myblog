<?php

namespace Tests\Feature;

use App\Models\User;
use Closure;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_dashboard_requires_an_administrator(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_administrator_can_log_in_and_out(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'correct-password']);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'correct-password',
        ])->assertRedirect('/admin');

        $this->get('/admin')->assertOk()->assertSee($admin->name);
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_non_administrator_and_wrong_password_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'correct-password']);

        foreach ([
            ['email' => $user->email, 'password' => 'correct-password'],
            ['email' => $admin->email, 'password' => 'wrong-password'],
        ] as $credentials) {
            $this->from('/admin/login')->post('/admin/login', $credentials)
                ->assertRedirect('/admin/login')
                ->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
                ->assertRedirect();
        }

        $this->post('/admin/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
            ->assertTooManyRequests();
    }

    public function test_admin_create_command_creates_an_administrator_with_a_hashed_password(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('名前', 'Test Admin')
            ->expectsQuestion('メールアドレス', 'admin@example.com')
            ->expectsQuestion('パスワード（12文字以上）', 'long-password-123')
            ->expectsQuestion('パスワード（確認）', 'long-password-123')
            ->assertSuccessful();

        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('long-password-123', $admin->password));
    }

    public function test_admin_create_command_rejects_invalid_password(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('名前', 'Test Admin')
            ->expectsQuestion('メールアドレス', 'admin@example.com')
            ->expectsQuestion('パスワード（12文字以上）', 'short')
            ->expectsQuestion('パスワード（確認）', 'short')
            ->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'admin@example.com']);
    }

    public function test_admin_seeder_creates_a_user_who_can_log_in(): void
    {
        $this->withAdminSeederEnvironment([
            'ADMIN_NAME' => 'Seed Admin',
            'ADMIN_EMAIL' => 'seed-admin@example.com',
            'ADMIN_PASSWORD' => 'long-password-123',
        ], function () {
            $this->artisan('db:seed', ['--class' => AdminSeeder::class])->assertSuccessful();

            $admin = User::where('email', 'seed-admin@example.com')->firstOrFail();
            $this->assertTrue($admin->is_admin);
            $this->assertTrue(Hash::check('long-password-123', $admin->password));
            $this->post('/admin/login', [
                'email' => $admin->email,
                'password' => 'long-password-123',
            ])->assertRedirect('/admin');
            $this->get('/admin')->assertOk();
        });
    }

    public function test_admin_seeder_rejects_missing_or_invalid_values(): void
    {
        $this->withAdminSeederEnvironment([
            'ADMIN_NAME' => null,
            'ADMIN_EMAIL' => 'seed-admin@example.com',
            'ADMIN_PASSWORD' => 'short',
        ], function () {
            try {
                $this->seed(AdminSeeder::class);
                $this->fail('Invalid administrator data was accepted.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('name', $exception->errors());
                $this->assertArrayHasKey('password', $exception->errors());
            }

            $this->assertDatabaseMissing('users', ['email' => 'seed-admin@example.com']);
        });
    }

    public function test_admin_seeder_does_not_change_an_existing_user(): void
    {
        $user = User::factory()->create(['email' => 'seed-admin@example.com']);

        $this->withAdminSeederEnvironment([
            'ADMIN_NAME' => 'Seed Admin',
            'ADMIN_EMAIL' => $user->email,
            'ADMIN_PASSWORD' => 'long-password-123',
        ], function () use ($user) {
            try {
                $this->seed(AdminSeeder::class);
                $this->fail('An existing email address was accepted.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('email', $exception->errors());
            }

            $user->refresh();
            $this->assertFalse($user->is_admin);
            $this->assertFalse(Hash::check('long-password-123', $user->password));
            $this->assertSame(1, User::where('email', $user->email)->count());
        });
    }

    private function withAdminSeederEnvironment(array $values, Closure $callback): void
    {
        $previous = [];
        foreach ($values as $key => $value) {
            $previous[$key] = getenv($key);
            putenv($value === null ? $key : "$key=$value");
        }

        try {
            $callback();
        } finally {
            foreach ($previous as $key => $value) {
                putenv($value === false ? $key : "$key=$value");
            }
        }
    }
}
