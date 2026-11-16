<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DragonDialogue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DragonDialogueController extends Controller
{
    public function index(): View
    {
        return view('admin.dragon-dialogues.index', [
            'dialogues' => DragonDialogue::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        DragonDialogue::create($this->validateDialogue($request));

        return redirect()->route('admin.dragon-dialogues.index')->with('status', '台詞を追加しました。');
    }

    public function edit(DragonDialogue $dragonDialogue): View
    {
        return view('admin.dragon-dialogues.edit', ['dialogue' => $dragonDialogue]);
    }

    public function update(Request $request, DragonDialogue $dragonDialogue): RedirectResponse
    {
        $dragonDialogue->update($this->validateDialogue($request));

        return redirect()->route('admin.dragon-dialogues.index')->with('status', '台詞を更新しました。');
    }

    public function destroy(DragonDialogue $dragonDialogue): RedirectResponse
    {
        $dragonDialogue->delete();

        return redirect()->route('admin.dragon-dialogues.index')->with('status', '台詞を削除しました。');
    }

    private function validateDialogue(Request $request): array
    {
        return $request->validate([
            'text' => ['required', 'string', 'max:40'],
        ], [
            'text.required' => '台詞を入力してください。',
            'text.string' => '台詞は文字列で入力してください。',
            'text.max' => '台詞は40文字以内で入力してください。',
        ]);
    }
}
