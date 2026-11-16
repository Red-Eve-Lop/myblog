<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach ([
                'touring' => 'ツーリング',
                'motorcycle' => 'バイクのこと',
                'daily' => '日々のこと',
            ] as $key => $name) {
                if (Category::where('sample_key', $key)->exists()) {
                    continue;
                }

                $category = Category::where('name', $name)->first() ?? new Category;
                if ($category->sample_key !== null) {
                    throw new RuntimeException("カテゴリー「{$name}」は別のサンプル記事に使用されています。");
                }

                $category->name = $name;
                $category->sample_key = $key;
                $category->save();
            }
        });
    }
}
