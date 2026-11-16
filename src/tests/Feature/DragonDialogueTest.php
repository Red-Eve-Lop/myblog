<?php

namespace Tests\Feature;

use App\Models\DragonDialogue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DragonDialogueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_dialogue_management_requires_an_administrator(): void
    {
        $dialogue = DragonDialogue::firstOrFail();

        $this->get('/admin/dragon-dialogues')->assertRedirect('/admin/login');
        $this->post('/admin/dragon-dialogues', ['text' => 'こんにちは'])->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());
        $this->get('/admin/dragon-dialogues')->assertForbidden();
        $this->post('/admin/dragon-dialogues', ['text' => 'こんにちは'])->assertForbidden();
        $this->get("/admin/dragon-dialogues/{$dialogue->id}/edit")->assertForbidden();
        $this->put("/admin/dragon-dialogues/{$dialogue->id}", ['text' => '変更'])->assertForbidden();
        $this->delete("/admin/dragon-dialogues/{$dialogue->id}")->assertForbidden();
        $this->assertDatabaseHas('dragon_dialogues', ['id' => $dialogue->id, 'text' => $dialogue->text]);
    }

    public function test_administrator_can_manage_dialogues_and_the_homepage_receives_them_in_order(): void
    {
        DragonDialogue::query()->delete();
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $this->post('/admin/dragon-dialogues', ['text' => 'はじめまして'])->assertRedirect('/admin/dragon-dialogues');
        $dialogue = DragonDialogue::firstOrFail();
        $this->post('/admin/dragon-dialogues', ['text' => 'またきてね'])->assertRedirect('/admin/dragon-dialogues');
        $this->get('/admin')->assertSee('台詞を管理する');
        $this->get('/admin/dragon-dialogues')->assertOk()->assertSee('はじめまして');
        $this->get("/admin/dragon-dialogues/{$dialogue->id}/edit")->assertOk()->assertSee('はじめまして');
        $this->put("/admin/dragon-dialogues/{$dialogue->id}", ['text' => 'ようこそ'])
            ->assertRedirect('/admin/dragon-dialogues');
        $this->get('/')->assertViewHas('dialogues', fn ($dialogues) => $dialogues->all() === ['ようこそ', 'またきてね']);

        $this->delete("/admin/dragon-dialogues/{$dialogue->id}")->assertRedirect('/admin/dragon-dialogues');
        $this->assertDatabaseMissing('dragon_dialogues', ['id' => $dialogue->id]);
        $this->get('/')->assertViewHas('dialogues', fn ($dialogues) => $dialogues->all() === ['またきてね']);
        DragonDialogue::query()->delete();
        $this->get('/')->assertViewHas('dialogues', fn ($dialogues) => $dialogues->isEmpty());
    }

    public function test_dialogues_must_be_nonempty_and_at_most_forty_characters(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $dialogue = DragonDialogue::firstOrFail();

        foreach (['', '   ', str_repeat('あ', 41)] as $text) {
            $this->post('/admin/dragon-dialogues', ['text' => $text])->assertSessionHasErrors('text');
            $this->put("/admin/dragon-dialogues/{$dialogue->id}", ['text' => $text])->assertSessionHasErrors('text');
        }

        $this->put("/admin/dragon-dialogues/{$dialogue->id}", ['text' => str_repeat('あ', 40)])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('dragon_dialogues', ['id' => $dialogue->id, 'text' => str_repeat('あ', 40)]);
    }
}
