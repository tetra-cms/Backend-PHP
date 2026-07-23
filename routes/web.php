<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

function serveFile(string $file)
{
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    $mime = match ($extension) {
        'js', 'mjs' => 'application/javascript',
        'css' => 'text/css',
        'json' => 'application/json',
        'map' => 'application/json',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'eot' => 'application/vnd.ms-fontobject',
        'wasm' => 'application/wasm',
        'txt' => 'text/plain',
        default => File::mimeType($file) ?: 'application/octet-stream',
    };

    return response()->file($file, [
        'Content-Type' => $mime,
    ]);
}

Route::get('/admin/{any?}', function (?string $any = null) {
    if ($any) {
        $file = public_path('admin/' . $any);

        if (File::exists($file) && File::isFile($file)) {
            return serveFile($file);
        }
    }

    return response()->file(public_path('admin/index.html'));
})->where('any', '.*');

Route::get('/{any?}', function (?string $any = null) {

    if ($any) {
        $file = public_path('app/' . $any);

        if (File::exists($file) && File::isFile($file)) {
            return serveFile($file);
        }
    }

    $html = File::get(public_path('app/index.html'));

    $title = config('app.title');
    $site_name = config('app.site_name');
    $description = config('app.description');
    $keywords = config('app.keywords');

    $image = url('/favicon.ico');
    $url = url($any ?? '/');

    $organization = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => config('app.title'),
        "url" => config('app.url'),
        "logo" => url('/favicon.ico'),
        "email" => config('app.email'),
        "telephone" => config('app.phone'),

        "address" => [
            "@type" => "PostalAddress",
            "addressCountry" => config('app.country'),
            "addressRegion" => config('app.region'),
            "addressLocality" => config('app.city'),
            "streetAddress" => config('app.address'),
        ],

        "contactPoint" => [[
            "@type" => "ContactPoint",
            "telephone" => config('app.phone'),
            "email" => config('app.email'),
            "contactType" => "customer support",
            "availableLanguage" => ["Russian"]
        ]]
    ];

    $seo = '
<title>'.e($title).'</title>

<meta name="description" content="'.e($description).'">

<meta name="keywords" content="'.e($keywords).'">

<meta name="robots" content="index, follow">

<meta name="author" content="'.e(config('app.title')).'">

<meta name="theme-color" content="#ffffff">

<link rel="canonical" href="'.e($url).'">

<meta property="og:title" content="'.e($title).'">

<meta property="og:site_name" content="'.e($site_name).'">

<meta property="og:description" content="'.e($description).'">

<meta property="og:type" content="website">

<link rel="icon" href="/favicon.ico" type="image/x-icon">

<meta property="og:url" content="'.e($url).'">

<meta property="og:image" content="'.e($image).'">

<meta name="twitter:card" content="summary_large_image">

<meta name="twitter:title" content="'.e($title).'">

<meta name="twitter:description" content="'.e($description).'">

<meta name="twitter:image" content="'.e($image).'">

<script type="application/ld+json">
'.json_encode(
        $organization,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_PRETTY_PRINT
    ).'
</script>
';

    $html = preg_replace(
        '/<head>/i',
        "<head>\n".$seo,
        $html,
        1
    );

    return response($html)
        ->header('Content-Type', 'text/html');

})->where('any', '^(?!api|admin).*$');
