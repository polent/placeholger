<?php
declare(strict_types=1);

/**
 * placeholger.hellinger.wtf — Main Front Controller & Router
 *
 * - Serves the canonical SEO-indexed Studio Landing Page on `/` (unless `?w=` is explicitly passed).
 * - Serves static files directly when run under PHP's built-in development server,
 *   enforcing same-origin / hotlink protection on `/placeholger.js`.
 * - Delegates all placeholder dimension paths (e.g., `/600/400/9e9ea0/ffffff?text=...`, `/512`, `/600x400`)
 *   to the zero-disk SVG engine (`api.php`) with strict `X-Robots-Tag: noindex, noimageindex` headers.
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = (string)parse_url($requestUri, PHP_URL_PATH);

/**
 * Rejects cross-site / remote Referer hotlinking of placeholger.js.
 * Direct downloads (no Referer and not Sec-Fetch-Site: cross-site) are permitted so users can self-host.
 */
function enforceSelfHostPolicyForSdk(): void
{
    $secFetchSite = strtolower(trim((string)($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '')));
    if ($secFetchSite === 'cross-site') {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        header('Cross-Origin-Resource-Policy: same-origin');
        echo "403 Forbidden: Remote loading of placeholger.js is not allowed. Please download and self-host a local copy.";
        exit;
    }

    $referer = trim((string)($_SERVER['HTTP_REFERER'] ?? ''));
    if ($referer !== '') {
        $refHost = strtolower((string)parse_url($referer, PHP_URL_HOST));
        $reqHost = strtolower((string)explode(':', (string)($_SERVER['HTTP_HOST'] ?? ''))[0]);
        $allowedHosts = array_filter(['placeholger.hellinger.wtf', 'www.placeholger.hellinger.wtf', 'localhost', '127.0.0.1', $reqHost]);

        if ($refHost === '' || !in_array($refHost, $allowedHosts, true)) {
            http_response_code(403);
            header('Content-Type: text/plain; charset=utf-8');
            header('Cross-Origin-Resource-Policy: same-origin');
            echo "403 Forbidden: Remote loading of placeholger.js is not allowed. Please download and self-host a local copy.";
            exit;
        }
    }
}

// Support PHP built-in server (`php -S`) serving static assets directly
if (PHP_SAPI === 'cli-server') {
    $staticFile = __DIR__ . $path;
    if ($path !== '/' && $path !== '/index.php' && $path !== '/api.php' && is_file($staticFile)) {
        if ($path === '/placeholger.js') {
            enforceSelfHostPolicyForSdk();
        }

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
            header('X-Content-Type-Options: nosniff');
            if ($path === '/placeholger.js') {
                header('Cross-Origin-Resource-Policy: same-origin');
            } else {
                header('Access-Control-Allow-Origin: *');
            }
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
