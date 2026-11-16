<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dragon_dialogues', function (Blueprint $table) {
            $table->id();
            $table->string('text', 40);
            $table->timestamps();
        });

        $now = now();
        DB::table('dragon_dialogues')->insert([
            ['text' => '遊びにきてくれてありがとう！', 'created_at' => $now, 'updated_at' => $now],
            ['text' => '今日はどこへ行こうかな？', 'created_at' => $now, 'updated_at' => $now],
            ['text' => 'のんびりしていってね。', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('dragon_dialogues');
    }
};
