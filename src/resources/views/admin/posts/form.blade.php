<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>記事{{ $post->exists ? '編集' : '作成' }} | ふわふわのブログ</title>
    @vite(['resources/css/admin.css', 'resources/js/post-editor.js'])
</head>
<body>
    <header class="dashboard-header">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">f.</span><span>ふわふわのブログ<small>ADMINISTRATION</small></span></a>
        <a class="back-link header-back" href="{{ route('admin.posts.index') }}">← 記事一覧へ</a>
    </header>
    <main class="dashboard-main post-editor-main">
        <p class="eyebrow">POSTS</p>
        <h1>記事{{ $post->exists ? '編集' : '作成' }}</h1>
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
        <noscript><p class="form-errors">記事の編集にはJavaScriptを有効にしてください。</p></noscript>
        <form id="post-form" class="category-panel" method="POST" action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}" data-upload-url="{{ route('admin.post-images.store') }}">
            @csrf
            @if ($post->exists) @method('PUT') @endif
            <label for="title">タイトル</label>
            <input id="title" name="title" value="{{ old('title', $post->title) }}" maxlength="255" required>

            <label for="editor">本文</label>
            <div class="editor-toolbar" role="toolbar" aria-label="本文の書式">
                <button type="button" data-command="heading2">見出し</button>
                <button type="button" data-command="heading3">小見出し</button>
                <button type="button" data-command="bold">太字</button>
                <button type="button" data-command="italic">斜体</button>
                <button type="button" data-command="bulletList">箇条書き</button>
                <button type="button" data-command="orderedList">番号付きリスト</button>
                <button type="button" data-command="blockquote">引用</button>
                <button type="button" data-command="link">リンク</button>
                <button type="button" data-command="unlink">リンク解除</button>
                <button type="button" data-command="undo">元に戻す</button>
                <button type="button" data-command="redo">やり直す</button>
                <button type="button" id="insert-image">本文に画像を挿入</button>
            </div>
            <div id="editor"></div>
            <textarea name="content" id="content" hidden>{{ old('content', $post->content) }}</textarea>
            <input id="body-image" type="file" accept="image/jpeg,image/png,image/gif,image/webp" hidden>
            <p class="editor-help">画像はJPEG・PNG・GIF・WebP、1枚2MB以下です。</p>

            <fieldset class="post-categories">
                <legend>カテゴリー（複数選択可）</legend>
                <input type="hidden" name="category_ids_present" value="1">
                @forelse ($categories as $category)
                    <label class="category-checkbox"><input type="checkbox" name="category_ids[]" value="{{ $category->id }}" @checked(in_array($category->id, old('category_ids', old('category_ids_present') ? [] : ($post->exists ? $post->categories->modelKeys() : []))))> {{ $category->name }}</label>
                @empty
                    <p class="category-empty">カテゴリーはまだありません。<a href="{{ route('admin.categories.index') }}">カテゴリーを管理する</a></p>
                @endforelse
            </fieldset>

            <label for="featured-image">アイキャッチ画像</label>
            <input id="featured-image" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            <input id="featured-image-id" name="featured_image_id" type="hidden" value="{{ old('featured_image_id', $post->featured_image_id) }}">
            <div id="featured-preview" @if (! old('featured_image_id', $post->featured_image_id)) hidden @endif>
                <img id="featured-preview-image" alt="アイキャッチのプレビュー" @if (old('featured_image_id', $post->featured_image_id)) src="{{ url('/post-images/'.old('featured_image_id', $post->featured_image_id)) }}" @endif>
                <button type="button" id="remove-featured">画像を外す</button>
            </div>
            <p id="upload-error" class="form-errors" role="alert" hidden></p>
            <p class="editor-help">「下書きで保存」は公開中の記事を非公開にします。公開中の記事の変更は「公開する」で反映されます。</p>
            <div class="post-save-actions">
                <button class="button button-secondary" type="submit" name="status" value="draft" disabled>下書きで保存</button>
                <button class="button button-primary" type="submit" name="status" value="published" disabled>公開する</button>
            </div>
        </form>
    </main>
</body>
</html>
