<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>ふわふわのブログ</title>
</head>
<body>
    <div id="app"></div>
    <script>
        window.blogCategories = {{ Illuminate\Support\Js::from($categories) }};
        window.blogPosts = {{ Illuminate\Support\Js::from($posts) }};
        window.dragonDialogues = {{ Illuminate\Support\Js::from($dialogues) }};
    </script>
</body>
</html>
