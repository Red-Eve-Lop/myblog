<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if ($post->status === 'private')
        <meta name="robots" content="noindex, nofollow">
    @endif
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
        @if ($post->status === 'private')
            <p class="section-kicker">非公開（管理者のみ閲覧できます）　／　<a href="{{ route('admin.posts.edit', $post) }}">記事を編集</a></p>
        @endif
        <p class="section-kicker">{{ $post->categories->pluck('name')->join('・') ?: '未分類' }}@if ($post->published_at)　／　{{ $post->published_at->format('Y.m.d') }}@endif</p>
        <h1>{{ $post->title }}</h1>
        @if ($post->featuredImage)
            <img class="article-featured" src="{{ route('post-images.show', $post->featuredImage) }}" alt="">
        @endif
        <article class="article-content">{!! $content !!}</article>
        @if ($post->status === 'published')
            <p class="article-share-wrap" hidden><a id="share-on-x" class="article-share" data-title="{{ $post->title }}" data-path="{{ route('posts.show', $post, false) }}" target="_blank" rel="noopener noreferrer"><span aria-hidden="true">𝕏</span> Xでシェアする</a></p>
            <noscript><p>XでシェアするにはJavaScriptを有効にしてください。</p></noscript>
            <script>
                const shareLink = document.querySelector('#share-on-x');
                const shareUrl = new URL('https://twitter.com/intent/tweet');
                shareUrl.searchParams.set('text', shareLink.dataset.title);
                shareUrl.searchParams.set('url', new URL(shareLink.dataset.path, window.location.origin).href);
                shareLink.href = shareUrl.href;
                shareLink.parentElement.hidden = false;
            </script>
        @endif
        <a class="article-back" href="{{ url('/#stories') }}">← 記事一覧へ</a>
    </main>
</body>
</html>
