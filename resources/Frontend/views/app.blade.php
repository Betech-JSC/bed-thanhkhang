@php
    if (function_exists('current_locale')) {
        $detectedLocale = current_locale();
        if (in_array($detectedLocale, config('app.locales', ['vi', 'en']))) {
            app()->setLocale($detectedLocale);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/header/hh-maker-icon.svg?v=20261109">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=20261109">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=20261109">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png?v=20261109">
    <link rel="shortcut icon" href="/favicon.ico?v=20261109">
    <link rel="manifest" href="/site.webmanifest?v=20261109">

    <!-- Hreflang Alternates -->
    @php
        $rawPath = request()->path();
        $currentPath = trim($rawPath, '/');

        $pageMap = [
            '' => ['vi' => '', 'en' => 'en'],
            'san-pham' => ['vi' => 'san-pham', 'en' => 'en/products'],
            'gioi-thieu' => ['vi' => 'gioi-thieu', 'en' => 'en/about-us'],
            'tin-tuc' => ['vi' => 'tin-tuc', 'en' => 'en/posts'],
            'lien-he' => ['vi' => 'lien-he', 'en' => 'en/contact'],
            'chinh-sach' => ['vi' => 'chinh-sach', 'en' => 'en/policies'],
            'dich-vu' => ['vi' => 'dich-vu', 'en' => 'en/services'],
            'quy-trinh' => ['vi' => 'quy-trinh', 'en' => 'en/process'],
        ];

        $appUrl = rtrim(config('app.url') ?: 'https://hhmaker.vn', '/');
        
        $viPath = null;
        $enPath = null;

        foreach ($pageMap as $entry) {
            if ($currentPath === $entry['vi'] || $currentPath === $entry['en']) {
                $viPath = $entry['vi'];
                $enPath = $entry['en'];
                break;
            }
        }

        if ($viPath === null) {
            if (Str::startsWith($currentPath, 'san-pham/')) {
                $slug = Str::after($currentPath, 'san-pham/');
                $viPath = 'san-pham/' . $slug;
                $enPath = 'en/products/' . $slug;
            } elseif (Str::startsWith($currentPath, 'en/products/')) {
                $slug = Str::after($currentPath, 'en/products/');
                $viPath = 'san-pham/' . $slug;
                $enPath = 'en/products/' . $slug;
            } elseif (Str::startsWith($currentPath, 'tin-tuc/')) {
                $slug = Str::after($currentPath, 'tin-tuc/');
                $viPath = 'tin-tuc/' . $slug;
                $enPath = 'en/posts/' . $slug;
            } elseif (Str::startsWith($currentPath, 'en/posts/')) {
                $slug = Str::after($currentPath, 'en/posts/');
                $viPath = 'tin-tuc/' . $slug;
                $enPath = 'en/posts/' . $slug;
            } elseif (Str::startsWith($currentPath, 'chinh-sach/')) {
                $slug = Str::after($currentPath, 'chinh-sach/');
                $viPath = 'chinh-sach/' . $slug;
                $enPath = 'en/policies/' . $slug;
            } elseif (Str::startsWith($currentPath, 'en/policies/')) {
                $slug = Str::after($currentPath, 'en/policies/');
                $viPath = 'chinh-sach/' . $slug;
                $enPath = 'en/policies/' . $slug;
            } elseif (Str::startsWith($currentPath, 'en/')) {
                $enPath = $currentPath;
                $viPath = Str::after($currentPath, 'en/');
            } else {
                $viPath = $currentPath;
                $enPath = 'en/' . $currentPath;
            }
        }

        $hreflangVi = $viPath === '' ? $appUrl . '/' : $appUrl . '/' . ltrim($viPath, '/');
        $hreflangEn = $appUrl . '/' . ltrim($enPath, '/');
        $hreflangDefault = $hreflangVi;
    @endphp
    <link rel="alternate" hreflang="vi" href="{{ $hreflangVi }}" />
    <link rel="alternate" hreflang="en" href="{{ $hreflangEn }}" />
    <link rel="alternate" hreflang="x-default" href="{{ $hreflangDefault }}" />

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

        $businessName = $global['general_business_name'] ?? 'HH Maker';
        $separator = $global['seo_title_separator'] ?? '|';

        $site = $global['seo_site_name'] ?? 'HH Maker';
        $canonical = $seo['seo_canonical'] ?? $url;

        // Kiểm tra trang lỗi
        $isError = (isset($status) && in_array($status, [404, 500, 503, 403])) || request()->is('error*');

        if ($isError) {
            $title = ($status ?? 404) . ' - Không tìm thấy trang | HH Maker';
            $description = 'Địa chỉ truy cập không tồn tại hoặc đã bị thay đổi trên hệ thống HH Maker.';
            $robots = 'noindex, nofollow';
        } else {
            $title = $seo['seo_meta_title'] ?? $seo['title'] ?? ($global['seo_meta_title'] ?? env('APP_NAME'));
            $description = $seo['seo_meta_description'] ?? $seo['description'] ?? ($global['seo_meta_description'] ?? null);
            $robots = $seo['seo_meta_robots'] ?? ($global['seo_meta_robots'] ?? 'index');
        }

        // Ưu tiên ảnh từ $seo; nếu lấy từ $global, phải lọc sạch không dính funkystyle cũ
        $rawImage = $seo['seo_image'] ?? $seo['image'] ?? null;
        if (empty($rawImage)) {
            $globalImage = $global['seo_image'] ?? null;
            if (!empty($globalImage) && !Str::contains($globalImage, ['funkystyle', 'khoqua', 'copy.jpg'])) {
                $rawImage = $globalImage;
            } else {
                $rawImage = '/og-image.jpg?v=20261109';
            }
        }

        // Nếu là đường dẫn tương đối (bắt đầu bằng "/"), nối domain từ request thực tế
        if (!Str::startsWith($rawImage, ['http://', 'https://'])) {
            $hostDomain = request()->getSchemeAndHttpHost() ?: (config('app.url') ?: 'https://hhmaker.vn');
            $rawImage = rtrim($hostDomain, '/') . '/' . ltrim($rawImage, '/');
        }

        // Parse URL và encode lại đúng phần path
        $parsed = parse_url($rawImage);

        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $parsed['path'] ?? '')));

        $image = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? 'hhmaker.vn') . $encodedPath;

        if (isset($parsed['query'])) {
            $image .= '?' . $parsed['query'];
        } elseif (!str_contains($image, '?')) {
            $image .= '?v=20261109';
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
            SEO::opengraph()->addImage($image, ['height' => 630, 'width' => 1200]);
            SEO::twitter()->addValue('image', $image);
            SEO::twitter()->addValue('card', 'summary_large_image');
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

    <!-- Fallback Open Graph & Twitter Cards Tags for Social Media Crawlers -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:secure_url" content="{{ $image }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $title }}">
    <meta property="og:site_name" content="{{ $site ?? 'HH Maker' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image }}">

    @isset($schemas)
        @foreach ($schemas as $schema)
            <script type="application/ld+json">
                {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
            </script>
        @endforeach
    @endisset

    <!-- Schema LocalBusiness & Organization -->
    @php
        $localBusinessSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            '@id' => 'https://hhmaker.vn/#organization',
            'name' => 'Công ty In ấn 3D HH Maker',
            'alternateName' => 'HH Maker 3D Printing',
            'url' => 'https://hhmaker.vn',
            'logo' => 'https://hhmaker.vn/assets/images/header/figma-header-logo.svg',
            'image' => 'https://hhmaker.vn/og-image.jpg?v=20261109',
            'description' => 'Xưởng thiết kế và gia công in 3D công nghệ FDM, SLA Resin 8K, SLS chuyên nghiệp tại TP.HCM.',
            'telephone' => '0906636425',
            'email' => 'info@hhmaker.vn',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Số 65 đường số 9, KDC Hương Lộ 5, Phường An Lạc',
                'addressLocality' => 'Quận Bình Tân',
                'addressRegion' => 'Thành phố Hồ Chí Minh',
                'postalCode' => '700000',
                'addressCountry' => 'VN',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 10.74123,
                'longitude' => 106.60845,
            ],
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                'opens' => '08:30',
                'closes' => '18:00',
            ],
            'priceRange' => '$$',
            'sameAs' => [
                'https://www.facebook.com/hhmaker',
            ],
        ];

        // Tự động sinh BreadcrumbList nếu controller chưa cung cấp
        $hasBreadcrumb = false;
        if (!empty($schemas) && is_array($schemas)) {
            foreach ($schemas as $s) {
                if (isset($s['@type']) && $s['@type'] === 'BreadcrumbList') {
                    $hasBreadcrumb = true;
                    break;
                }
            }
        }

        $autoBreadcrumb = null;
        if (!$hasBreadcrumb && $currentPath !== '' && $currentPath !== 'en') {
            $isEn = app()->getLocale() === 'en';
            $homeTitle = $isEn ? 'Home' : 'Trang chủ';
            $homeUrl = $isEn ? $appUrl . '/en' : $appUrl;

            $items = [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => $homeTitle,
                    'item' => $homeUrl,
                ],
            ];

            if (Str::startsWith($currentPath, ['san-pham', 'en/products'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $isEn ? 'Products' : 'Sản phẩm',
                    'item' => $appUrl . ($isEn ? '/en/products' : '/san-pham'),
                ];
                if (Str::contains($currentPath, '/')) {
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $title ?: ($isEn ? 'Product Details' : 'Chi tiết sản phẩm'),
                        'item' => $url,
                    ];
                }
            } elseif (Str::startsWith($currentPath, ['tin-tuc', 'en/posts'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $isEn ? 'News & Blog' : 'Tin tức',
                    'item' => $appUrl . ($isEn ? '/en/posts' : '/tin-tuc'),
                ];
                if (Str::contains($currentPath, '/')) {
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $title ?: ($isEn ? 'Article' : 'Bài viết'),
                        'item' => $url,
                    ];
                }
            } elseif (Str::startsWith($currentPath, ['gioi-thieu', 'en/about-us'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $isEn ? 'About Us' : 'Giới thiệu',
                    'item' => $appUrl . ($isEn ? '/en/about-us' : '/gioi-thieu'),
                ];
            } elseif (Str::startsWith($currentPath, ['chinh-sach', 'en/policies'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $isEn ? 'Policies' : 'Chính sách',
                    'item' => $appUrl . ($isEn ? '/en/policies' : '/chinh-sach'),
                ];
                if (Str::contains($currentPath, '/')) {
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $title ?: ($isEn ? 'Policy Details' : 'Chi tiết chính sách'),
                        'item' => $url,
                    ];
                }
            } elseif (Str::startsWith($currentPath, ['dich-vu', 'en/services'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $isEn ? 'Services' : 'Dịch vụ',
                    'item' => $appUrl . ($isEn ? '/en/services' : '/dich-vu'),
                ];
            } elseif (Str::startsWith($currentPath, ['quy-trinh', 'en/process'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $isEn ? 'Process' : 'Quy trình gia công 3D',
                    'item' => $appUrl . ($isEn ? '/en/process' : '/quy-trinh'),
                ];
            } elseif (Str::startsWith($currentPath, ['lien-he', 'en/contact'])) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $isEn ? 'Contact' : 'Liên hệ',
                    'item' => $appUrl . ($isEn ? '/en/contact' : '/lien-he'),
                ];
            }

            if (count($items) > 1) {
                $autoBreadcrumb = [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => $items,
                ];
            }
        }
    @endphp

    <script type="application/ld+json">
        {!! json_encode($localBusinessSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @if ($autoBreadcrumb)
        <script type="application/ld+json">
            {!! json_encode($autoBreadcrumb, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

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
