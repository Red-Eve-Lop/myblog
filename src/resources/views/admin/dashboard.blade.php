<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>管理画面 | ふわふわのブログ</title>
    @vite('resources/css/admin.css')
</head>
<body class="dashboard-page">
    <header class="dashboard-header">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">f.</span><span>ふわふわのブログ<small>ADMINISTRATION</small></span></a>
        <div class="header-actions">
            <a href="{{ url('/') }}">ブログを見る ↗</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="logout">ログアウト</button>
            </form>
        </div>
    </header>
    <main class="dashboard-main">
        <p class="eyebrow">YOUR LITTLE WORKSPACE</p>
        <h1>管理画面</h1>
        <p class="greeting">{{ auth()->user()->name }}さん、おかえりなさい。</p>
        <section class="welcome-panel">
            <div class="welcome-icon" aria-hidden="true">✳</div>
            <div>
                <p class="eyebrow">TAKE IT EASY</p>
                <h2>ブログを育てる場所。</h2>
                <p>ツーリングの記録や、日々のことを綴るための管理画面です。</p>
            </div>
        </section>
        <section class="features" aria-labelledby="features-title">
            <div class="section-header"><div><p class="eyebrow">WHAT YOU CAN DO</p><h2 id="features-title">できること</h2></div></div>
            <div class="feature-grid">
                <article class="feature-card"><span class="feature-number">01</span><h3>記事の管理</h3><p>記事の作成や編集ができます。</p><a class="status" href="{{ route('admin.posts.index') }}">記事を管理する →</a></article>
                <article class="feature-card"><span class="feature-number">02</span><h3>カテゴリーの管理</h3><p>カテゴリーの追加や編集ができます。</p><a class="status" href="{{ route('admin.categories.index') }}">カテゴリーを管理する →</a></article>
                <article class="feature-card"><span class="feature-number">03</span><h3>ドラゴンの台詞</h3><p>トップページに表示する台詞を管理できます。</p><a class="status" href="{{ route('admin.dragon-dialogues.index') }}">台詞を管理する →</a></article>
            </div>
        </section>
    </main>
    <footer class="dashboard-footer">ふわふわのブログ <span>／</span> ADMINISTRATION</footer>
</body>
</html>
