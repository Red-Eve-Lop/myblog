<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理人について | ふわふわのブログ</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="site-shell">
        <header class="site-header">
            <div class="header-inner">
                <a class="brand" href="{{ url('/') }}" aria-label="ふわふわのブログ トップへ">
                    <span class="brand-mark" aria-hidden="true">f<span>.</span></span>
                    <span class="brand-copy"><strong>ふわふわのブログ</strong><small>MOTORCYCLE & EVERYDAY</small></span>
                </a>
                <nav class="main-nav" aria-label="メインナビゲーション">
                    <a href="{{ url('/#stories') }}">記事</a>
                    <a href="{{ route('about') }}" aria-current="page">管理人について</a>
                </nav>
            </div>
        </header>

        <main class="profile-main">
            <section class="profile-intro" aria-labelledby="profile-title">
                <p class="section-kicker"><span>ABOUT THE AUTHOR</span></p>
                <h1 id="profile-title">このブログを書いている人</h1>
                <p>はじめまして。「ふわふわのブログ」を書いている管理人です。バイクで出かけた日のことも、いつもの暮らしのことも、気ままに綴っています。</p>
            </section>

            <section class="profile-card" aria-labelledby="profile-name">
                <div class="profile-emblem" aria-hidden="true">f<span>.</span></div>
                <div class="profile-details">
                    <p class="section-kicker">A LITTLE ABOUT ME</p>
                    <h2 id="profile-name">ふわふわドラゴン</h2>
                    <p>ふわふわのブログの管理人です。</p>
                    <p>遠回りして見つけた景色や、何気ない毎日の小さな発見が好きです。ここでは、ツーリングの記録や日々のことを、自分のペースで残していきます。</p>
                    <div class="profile-interests">
                        <h3>このブログに書いていること</h3>
                        <ul>
                            <li>バイクとツーリング</li>
                            <li>日々の暮らし</li>
                            <li>好きなもののこと</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="profile-note" aria-labelledby="profile-note-title">
                <h2 id="profile-note-title">のんびり、マイペースに。</h2>
                <p>気になる記事があれば、ぜひ読んでいってください。</p>
                <a class="hero-link" href="{{ url('/#stories') }}">記事を読んでみる <span aria-hidden="true">↗</span></a>
            </section>
        </main>

        <footer class="site-footer">
            <div class="footer-inner">
                <div><strong>ふわふわのブログ<span>.</span></strong><p>バイクと、日々のこと。</p></div>
                <a href="{{ url('/') }}">ブログトップへ <span aria-hidden="true">↗</span></a>
            </div>
            <div class="footer-bottom"><span>FREELY, AT YOUR OWN PACE.</span><span>ふわふわのブログ</span></div>
        </footer>
    </div>
</body>
</html>
