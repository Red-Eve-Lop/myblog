<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>ドラゴンの台詞 | ふわふわのブログ</title>
    @vite('resources/css/admin.css')
</head>
<body>
    <header class="dashboard-header">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">f.</span><span>ふわふわのブログ<small>ADMINISTRATION</small></span></a>
        <a class="back-link header-back" href="{{ route('admin.dashboard') }}">← 管理画面へ</a>
    </header>
    <main class="dashboard-main category-main">
        <p class="eyebrow">DRAGON'S WORDS</p>
        <h1>ドラゴンの台詞</h1>
        <p class="greeting">台詞は登録順に8秒ごとに切り替わります。台詞がない場合は吹き出しを表示しません。</p>
        @if (session('status'))
            <p class="form-success" role="status">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <div class="form-errors" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <section class="category-panel">
            <h2>台詞を追加</h2>
            <form method="POST" action="{{ route('admin.dragon-dialogues.store') }}">
                @csrf
                <label for="text">台詞（40文字以内）</label>
                <input id="text" name="text" value="{{ old('text') }}" maxlength="40" required>
                <button class="button button-primary category-submit" type="submit">追加する</button>
            </form>
        </section>
        <section class="category-panel">
            <h2>台詞一覧</h2>
            @forelse ($dialogues as $dialogue)
                <div class="category-row">
                    <span class="dialogue-text">{{ $dialogue->text }}</span>
                    <div class="category-actions">
                        <a href="{{ route('admin.dragon-dialogues.edit', $dialogue) }}">編集</a>
                        <form method="POST" action="{{ route('admin.dragon-dialogues.destroy', $dialogue) }}" onsubmit="return confirm('この台詞を削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="category-delete">削除</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="category-empty">台詞はまだありません。</p>
            @endforelse
        </section>
    </main>
</body>
</html>
