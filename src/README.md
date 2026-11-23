<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 管理画面

管理画面は `/admin` です。通常のユーザーはアクセスできず、管理者権限のあるアカウントが必要です。

Docker環境で、リポジトリのルートから次のコマンドを実行してください。

```sh
docker compose exec php php artisan migrate --path=database/migrations/2026_11_15_000000_add_is_admin_to_users_table.php
docker compose exec php php artisan admin:create
```

アカウント作成時は名前、メールアドレス、12文字以上のパスワードを対話形式で入力します。複数の管理者を追加する場合は、作成コマンドを人数分実行してください。既存アカウントの権限変更はこのコマンドでは行いません。

シーダーで作成する場合は、マイグレーション適用後に次のコマンドを実行します。値はシェルで入力し、コンテナに環境変数として渡します。

```sh
read -r -p '名前: ' ADMIN_NAME
read -r -p 'メールアドレス: ' ADMIN_EMAIL
read -r -s -p 'パスワード（12文字以上）: ' ADMIN_PASSWORD; echo
export ADMIN_NAME ADMIN_EMAIL ADMIN_PASSWORD
docker compose exec -e ADMIN_NAME -e ADMIN_EMAIL -e ADMIN_PASSWORD php php artisan db:seed --class=AdminSeeder
unset ADMIN_NAME ADMIN_EMAIL ADMIN_PASSWORD
```

シーダーは明示的に実行した場合のみ管理者を作成します。同じメールアドレスが既に存在する場合はエラーとなり、既存ユーザーは変更しません。

## カテゴリー管理

カテゴリーをDBで管理するには、リポジトリのルートから次を実行します。

```sh
docker compose exec php php artisan migrate
docker compose exec php php artisan db:seed --class=CategorySeeder
```

シーダーはサンプル記事に対応する3カテゴリーを登録します。同名のカテゴリーが既にあれば再利用し、再実行しても名前を上書きしません。`sample_key` はサンプル記事との対応を維持するための識別子です。管理画面の「カテゴリーを管理する」から追加・編集できます。記事やサンプル記事に使われているカテゴリーは削除できません。

## 記事の投稿

依存パッケージのインストールとマイグレーションを実行してください。

```sh
docker compose exec php composer install
docker compose exec node npm ci
docker compose exec php php artisan migrate
docker compose exec php mkdir -p storage/app/private/post-images
docker compose exec php chgrp www-data storage/app/private/post-images
docker compose exec php chmod g+rwx storage/app/private/post-images
docker compose exec node npm run build
```

管理画面の「記事を管理する」から、記事の作成・編集、複数カテゴリーの選択、下書き・非公開・公開の切り替えができます。非公開の記事は管理者だけが詳細ページを閲覧できます。公開中の記事を下書きまたは非公開で保存すると一般に表示されなくなります。本文とアイキャッチにJPEG・PNG・GIF・WebP画像（1枚2MB以下）を使えます。

公開記事の「Xでシェアする」から、記事タイトルとURLが入ったXの投稿画面を開けます。投稿は自動ではなく、X上で内容を確認して行います。非公開記事には表示されません。

公開記事にはSNSのリンクカード用メタ情報が付きます。画像はアイキャッチを使用し、未設定の場合はドラゴンの画像を使用します。公開環境では記事と画像のHTTPS URLにSNSのクローラーがアクセスできる必要があります。リバースプロキシを使用する場合は、Laravelが外部のHTTPS URLを正しく生成できるよう設定してください。カードの表示や更新は各SNSの仕様・キャッシュに依存します。

画像は `storage/app/private/post-images` に保存され、公開記事に紐付いているものだけが外部に配信されます。下書き・非公開の記事や未保存の画像は管理者のみ閲覧できます。記事から取り外した画像も非公開になりますが、ファイルは自動削除されません。既存の記事データはマイグレーション後、下書きとして扱います。

Nginxの設定を変更した場合はコンテナに反映し、再読み込みしてください。別のWebサーバーやPHP設定を使う場合も、画像を2MBまで受け付けられるようリクエストサイズを設定してください。

公開側と管理側で同じ `public` ディレクトリを使えますが、Nginxの `/admin` の設定自体は認証ではありません。管理画面のファイルは `public/admin` に直接置かず、Laravelの認証・権限チェックを通してください。公開環境ではHTTPSを使用し、セキュアなセッションCookieを設定してください。

## ドラゴンの台詞

`docker compose exec php php artisan migrate` で台詞用テーブルと初期台詞を作成します。管理画面の「台詞を管理する」から40文字以内の台詞を追加・編集・削除できます。トップページには登録順に表示し、複数ある場合は8秒ごとに切り替わります。すべて削除すると吹き出しは表示されません。

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
