<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>カテゴリー管理 | ふわふわのブログ</title>
    @vite('resources/css/admin.css')
</head>
<body>
    <header class="dashboard-header">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">f.</span><span>ふわふわのブログ<small>ADMINISTRATION</small></span></a>
        <a class="back-link header-back" href="{{ route('admin.dashboard') }}">← 管理画面へ</a>
    </header>
    <main class="dashboard-main category-main">
        <p class="eyebrow">CATEGORIES</p>
        <h1>カテゴリーの管理</h1>
        <p class="greeting">カテゴリーを追加・編集できます。記事に使われているカテゴリーは削除できません。</p>

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
            <h2>カテゴリーを追加</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <label for="name">カテゴリー名</label>
                <input id="name" name="name" value="{{ old('name') }}" maxlength="255" required>
                <button class="button button-primary category-submit" type="submit">追加する</button>
            </form>
        </section>

        <section class="category-panel">
            <h2>カテゴリー一覧</h2>
            @forelse ($categories as $category)
                <div class="category-row">
                    <span>{{ $category->name }}</span>
                    <div class="category-actions">
                        <a href="{{ route('admin.categories.edit', $category) }}">編集</a>
                        @if ($category->posts_count || $category->sample_key)
                            <span class="category-in-use">記事で使用中</span>
                        @else
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('このカテゴリーを削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="category-delete">削除</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="category-empty">カテゴリーはまだありません。</p>
            @endforelse
        </section>
    </main>
</body>
</html>
