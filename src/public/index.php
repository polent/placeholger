<?php
declare(strict_types=1);

/**
 * placeholger.hellinger.wtf — Main Front Controller & Router
 *
 * - Serves the canonical SEO-indexed Studio Landing Page on `/` (unless `?w=` is explicitly passed).
 * - Serves static files directly when run under PHP's built-in development server.
 * - Delegates all placeholder dimension paths (e.g., `/600/400/9e9ea0/ffffff?text=...`, `/512`, `/600x400`)
 *   to the zero-disk SVG engine (`api.php`) with strict `X-Robots-Tag: noindex, noimageindex` headers.
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = (string)parse_url($requestUri, PHP_URL_PATH);

// Support PHP built-in server (`php -S`) serving static assets directly
if (PHP_SAPI === 'cli-server') {
    $staticFile = __DIR__ . $path;
    if ($path !== '/' && $path !== '/index.php' && $path !== '/api.php' && is_file($staticFile)) {
        // Attach right MIME type for special agentic/spec files if needed
        $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
        $mimeMap = [
            'css'   => 'text/css; charset=utf-8',
            'js'    => 'application/javascript; charset=utf-8',
            'json'  => 'application/json; charset=utf-8',
            'txt'   => 'text/plain; charset=utf-8',
            'xml'   => 'application/xml; charset=utf-8',
            'svg'   => 'image/svg+xml; charset=utf-8',
            'woff2' => 'font/woff2',
        ];
        if (isset($mimeMap[$ext])) {
            header('Content-Type: ' . $mimeMap[$ext]);
            header('Access-Control-Allow-Origin: *');
            readfile($staticFile);
            exit;
        }
        return false;
    }
}

$trimmedPath = trim($path, '/');

// If the request is for the root landing page `/` (and no explicit `?w=` image parameter is requested)
if (($trimmedPath === '' || $trimmedPath === 'index.php') && !isset($_GET['w'])) {
    header('Content-Type: text/html; charset=utf-8');
    header('X-Robots-Tag: index, follow, max-image-preview:large, max-snippet:-1');
    header('Cache-Control: public, max-age=3600, stale-while-revalidate=86400');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Link: </llms.txt>; rel="alternate"; type="text/markdown", </openapi.json>; rel="service-desc"; type="application/vnd.oai.openapi+json"');

    $landingView = dirname(__DIR__) . '/views/landing.php';
    if (!is_file($landingView)) {
        // Fallback when deployed flat inside dist/
        $landingView = __DIR__ . '/views/landing.php';
    }
    require $landingView;
    exit;
}

// Otherwise, route to the Zero-Disk SVG Generator Engine
define('PLACEHOLGER_EXECUTE_API', true);
require __DIR__ . '/api.php';
