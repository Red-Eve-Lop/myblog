<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>記事管理 | ふわふわのブログ</title>
    @vite('resources/css/admin.css')
</head>
<body>
    <header class="dashboard-header">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">f.</span><span>ふわふわのブログ<small>ADMINISTRATION</small></span></a>
        <a class="back-link header-back" href="{{ route('admin.dashboard') }}">← 管理画面へ</a>
    </header>
    <main class="dashboard-main category-main">
        <p class="eyebrow">POSTS</p>
        <h1>記事の管理</h1>
        <a class="status" href="{{ route('admin.posts.create') }}">記事を作成する →</a>
        <section class="category-panel">
            <h2>記事一覧</h2>
            @forelse ($posts as $post)
                <div class="category-row">
                    <span>{{ $post->title }} <small class="category-in-use">（{{ match ($post->status) { 'published' => '公開中', 'private' => '非公開', 'draft' => '下書き', default => '不明' } }}）</small></span>
                    <div class="category-actions"><a href="{{ route('admin.posts.edit', $post) }}">編集</a></div>
                </div>
            @empty
                <p class="category-empty">記事はまだありません。</p>
            @endforelse
        </section>
    </main>
</body>
</html>
