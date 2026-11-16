<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} | ふわふわのブログ</title>
    @vite('resources/css/app.css')
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ url('/') }}"><span class="brand-mark">f<span>.</span></span><span class="brand-copy"><strong>ふわふわのブログ</strong><small>MOTORCYCLE & EVERYDAY</small></span></a>
            <a href="{{ url('/#stories') }}">← 記事一覧へ</a>
        </div>
    </header>
    <main class="article-main">
        <p class="section-kicker">{{ $post->categories->pluck('name')->join('・') ?: '未分類' }}　／　{{ $post->published_at?->format('Y.m.d') }}</p>
        <h1>{{ $post->title }}</h1>
        @if ($post->featuredImage)
            <img class="article-featured" src="{{ route('post-images.show', $post->featuredImage) }}" alt="">
        @endif
        <article class="article-content">{!! $content !!}</article>
        <a class="article-back" href="{{ url('/#stories') }}">← 記事一覧へ</a>
    </main>
</body>
</html>
