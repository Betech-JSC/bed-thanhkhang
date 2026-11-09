<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex" />

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/header/hh-maker-icon.svg?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2">
    <link rel="shortcut icon" href="/favicon.ico?v=2">

    <script src="/tinymce/tinymce.min.js"></script>

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    {{ Vite::useHotFile(public_path('vite.backend.hot'))->useBuildDirectory('build/backend')->withEntryPoints(['resources/Backend/js/app.js']) }}

    @routes
</head>

<body>
    @inertia
</body>

</html>