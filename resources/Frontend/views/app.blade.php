<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/header/hh-maker-icon.svg?v=2">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=2">
    <link rel="shortcut icon" href="/favicon.ico?v=2">
    <link rel="manifest" href="/site.webmanifest?v=2">

    @php
        try {
            $global = \Illuminate\Support\Facades\Schema::hasTable('settings')
                ? cache_response(
                    'global_settings',
                    function () {
                        return settings()
                            ->group('general')
                            ->all();
                    },
                    'settings',
                )
                : [];
        } catch (\Throwable $e) {
            $global = [];
        }
    @endphp

    <!-- Custom Header Tag -->
    @php
        echo $global['inject_head'] ?? null;
    @endphp
    <!-- End Custom Header Tag -->

    @php
        $url = url()->current();

        $businessName = $global['general_business_name'] ?? null;
        $separator = $global['seo_title_separator'] ?? null;

        $site = $global['seo_site_name'] ?? null;
        $canonical = $seo['seo_canonical'] ?? $url;

        $title = $seo['seo_meta_title'] ?? $seo['title'] ?? ($global['seo_meta_title'] ?? env('APP_NAME'));
        $description = $seo['seo_meta_description'] ?? $seo['description'] ?? ($global['seo_meta_description'] ?? null);
        $robots = $seo['seo_meta_robots'] ?? ($global['seo_meta_robots'] ?? 'index');
        $rawImage = $seo['seo_image'] ?? $seo['image'] ?? ($global['seo_image'] ?? '/cover.jpg?v=2');

        // Nếu là đường dẫn tương đối (bắt đầu bằng "/"), nối domain
        if (!Str::startsWith($rawImage, ['http://', 'https://'])) {
            $rawImage = config('app.url') . '/' . ltrim($rawImage, '/');
        }

        // Parse URL và encode lại đúng phần path
        $parsed = parse_url($rawImage);

        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $parsed['path'] ?? '')));

        $image = ($parsed['scheme'] ?? 'http') . '://' . ($parsed['host'] ?? 'localhost') . $encodedPath;

        if (isset($parsed['query'])) {
            $image .= '?' . $parsed['query'];
        }
        $keywords = $seo['seo_meta_keywords'] ?? ($global['seo_meta_keywords'] ?? null);

        if ($title) {
            SEOMeta::setTitle($title);
            SEO::setTitle($title);
            SEO::jsonLd()->setTitle($title);
        }

        if ($description) {
            SEOMeta::setDescription($description);
            SEO::setDescription($description);
            SEO::jsonLd()->setDescription($description);
        }

        if ($keywords) {
            SEOMeta::addKeyword($keywords);
            SEO::metatags()->setKeywords($keywords);
        }

        if ($robots) {
            SEOMeta::setRobots($robots);
            SEO::metatags()->setRobots($robots);
        }

        if ($image) {
            SEO::opengraph()->addImage($image);
            SEO::twitter()->addValue('image', $image);
            SEO::jsonLd()->setImage($image);
        }

        if ($site) {
            SEO::opengraph()->setSiteName($site);
            SEO::twitter()->setSite($site);
            SEO::jsonLd()->setSite($site);
        }

        SEOMeta::setCanonical($url);
        SEO::opengraph()->setUrl($url);
        SEO::setCanonical($canonical);
        SEO::jsonLd()->setUrl($url);

        $schemas = $seo['seo_schemas'] ?? null;
        if (is_string($schemas)) {
            $schemas = json_decode($schemas, true);
        }
    @endphp

    {!! SEO::generate() !!}

    @isset($schemas)
        @foreach ($schemas as $schema)
            <script type="application/ld+json">
                {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
            </script>
        @endforeach
    @endisset

    <script src="/build/frontend/registerSW.js"></script>

        <!-- Fonts (Be Vietnam Pro & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/assets/js/flickity/flickity.min.css" />
    <script src="/assets/js/flickity/flickity.pkgd.min.js"></script>

    <!-- CSS -->

    <style>
        /* flickity-fade */
        .flickity-enabled.is-fade .flickity-slider > * {
            pointer-events: none;
            z-index: 0;
        }

        .flickity-enabled.is-fade .flickity-slider > .is-selected {
            pointer-events: auto;
            z-index: 1;
        }
    </style>


    {{ Vite::useHotFile(public_path('vite.frontend.hot'))->useBuildDirectory('build/frontend')->withEntryPoints(['resources/Frontend/js/app.js']) }}
    @inertiaHead

    <!-- @routes('frontend') -->
</head>

<body class="antialiased">
    <!-- Custom Header Body Tag -->
    @if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false)
        @php
            echo $global['inject_body_start'] ?? null;
        @endphp
    @endif
    <!-- End Custom Header Body Tag -->

    @inertia

    <!-- Custom Footer Body Tag -->
    @if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false)
        @php
            echo $global['inject_body_end'] ?? null;
        @endphp
    @endif
    <!-- End Custom Footer Body Tag -->
</body>

</html>
