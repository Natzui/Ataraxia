<?php /** @var string|null $title */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ? e($title) . ' · ' : '' ?><?= e(config('app_name')) ?></title>
    <link rel="icon" href="data:,">
    <script>
    (function () {
        var t = 'light';
        try {
            t = localStorage.getItem('theme') ||
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        } catch (e) {}
        document.documentElement.setAttribute('data-bs-theme', t === 'dark' ? 'dark' : 'light');
    })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/style.css')) ?>?v=<?= (int) @filemtime(PUBLIC_PATH . '/assets/css/style.css') ?>" rel="stylesheet">
</head>
<body></body>
