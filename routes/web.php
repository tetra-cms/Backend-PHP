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
    $description = config('app.description');
    $image = "/favicon.ico";

    $seo = "
        <title>" . e($title) . "</title>

        <meta name=\"description\" content=\"" . e($description) . "\">

        <meta property=\"og:title\" content=\"" . e($title) . "\">

        <meta property=\"og:site_name\" content=\"" . e($title) . "\">

        <meta property=\"og:description\" content=\"" . e($description) . "\">

        <meta property=\"og:type\" content=\"website\">
    ";

    if ($image) {
        $seo .= "\n<meta property=\"og:image\" content=\"" . e($image) . "\">";
    }

    $html = preg_replace(
        '/<head>/i',
        "<head>\n{$seo}",
        $html,
        1
    );

    return response($html)
        ->header('Content-Type', 'text/html');

})->where('any', '^(?!api|admin).*$');
