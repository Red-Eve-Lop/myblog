<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>ログイン | ふわふわのブログ 管理画面</title>
    @vite('resources/css/admin.css')
</head>
<body class="login-page">
    <main class="login-card">
        <a class="brand" href="{{ url('/') }}"><span class="brand-mark">f.</span><span>ふわふわのブログ<small>ADMINISTRATION</small></span></a>
        <div class="login-heading">
            <p class="eyebrow">WELCOME BACK</p>
            <h1>管理画面へログイン</h1>
            <p>管理者のアカウントでログインしてください。</p>
        </div>
        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            @if ($errors->any())
                <div class="form-errors" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <label for="email">メールアドレス</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
            <label for="password">パスワード</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <button type="submit" class="button button-primary">ログイン <span aria-hidden="true">→</span></button>
        </form>
        <a class="back-link" href="{{ url('/') }}">← ブログに戻る</a>
    </main>
</body>
</html>
