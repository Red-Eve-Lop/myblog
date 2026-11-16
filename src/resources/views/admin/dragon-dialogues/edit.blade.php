<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>台詞の編集 | ふわふわのブログ</title>
    @vite('resources/css/admin.css')
</head>
<body>
    <header class="dashboard-header">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">f.</span><span>ふわふわのブログ<small>ADMINISTRATION</small></span></a>
        <a class="back-link header-back" href="{{ route('admin.dragon-dialogues.index') }}">← 台詞一覧へ</a>
    </header>
    <main class="dashboard-main category-main">
        <p class="eyebrow">DRAGON'S WORDS</p>
        <h1>台詞の編集</h1>
        <section class="category-panel">
            @if ($errors->any())
                <div class="form-errors" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.dragon-dialogues.update', $dialogue) }}">
                @csrf
                @method('PUT')
                <label for="text">台詞（40文字以内）</label>
                <input id="text" name="text" value="{{ old('text', $dialogue->text) }}" maxlength="40" required>
                <button class="button button-primary category-submit" type="submit">保存する</button>
            </form>
        </section>
    </main>
</body>
</html>
