<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('posts')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validateName($request));

        return redirect()->route('admin.categories.index')->with('status', 'カテゴリーを追加しました。');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validateName($request, $category));

        return redirect()->route('admin.categories.index')->with('status', 'カテゴリーを更新しました。');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $deleted = DB::transaction(function () use ($category) {
            $category = Category::whereKey($category->id)->lockForUpdate()->firstOrFail();

            if ($category->sample_key !== null || $category->posts()->exists()) {
                return false;
            }

            $category->delete();

            return true;
        });

        if (! $deleted) {
            return redirect()->route('admin.categories.index')
                ->withErrors(['category' => '記事に使われているカテゴリーは削除できません。']);
        }

        return redirect()->route('admin.categories.index')->with('status', 'カテゴリーを削除しました。');
    }

    private function validateName(Request $request, ?Category $category = null): array
    {
        $unique = Rule::unique('categories', 'name');
        if ($category !== null) {
            $unique->ignore($category->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'not_in:すべて', $unique],
        ], [
            'name.required' => 'カテゴリー名を入力してください。',
            'name.string' => 'カテゴリー名は文字列で入力してください。',
            'name.max' => 'カテゴリー名は255文字以内で入力してください。',
            'name.not_in' => '「すべて」はカテゴリー名に使用できません。',
            'name.unique' => 'このカテゴリー名は既に使われています。',
        ]);
    }
}
