<?php
declare(strict_types=1);

/**
 * placeholger.hellinger.wtf — Zero-Disk Dynamic SVG Wireframe Engine
 *
 * Generates mathematically crisp vector wireframes matching the original
 * reference design (grafik.png): neutral background, corner-to-center
 * diagonal crosshairs with a clean center gap around the uppercase text label,
 * and optional dimensions subtitle.
 *
 * Emits strict non-indexing headers (X-Robots-Tag: noindex, noimageindex)
 * and immutable edge caching headers.
 */

namespace Placeholger;

class SvgGenerator
{
    public const MIN_DIM = 1;
    public const MAX_DIM = 2048;
    public const DEFAULT_W = 600;
    public const DEFAULT_H = 400;
    public const DEFAULT_BG = '9E9EA0';
    public const DEFAULT_FG = 'FFFFFF';
    public const DEFAULT_TEXT = 'PLACEHOLGER';

    /**
     * Parses request URI path and query parameters into validated SVG options.
     *
     * Supported path formats:
     *   /{w}/{h}/{bg}/{fg}
     *   /{w}/{h}/{bg}
     *   /{w}/{h}
     *   /{size}/{bg}/{fg}  (when 2nd segment is a 3 or 6 char hex non-numeric or when only 1 numeric segment is passed)
     *   /{size}
     *
     * @param string $requestPath URL path (e.g., "/600/400/9e9ea0/ffffff")
     * @param array<string, mixed> $query Query parameters ($_GET)
     * @return array{
     *   status: int,
     *   error?: string,
     *   w: int,
     *   h: int,
     *   bg: string,
     *   fg: string,
     *   text: string,
     *   showDims: bool,
     *   etag: string
     * }
     */
    public static function parseRequest(string $requestPath, array $query): array
    {
        $cleanPath = trim( (string)parse_url($requestPath, PHP_URL_PATH), '/' );
        // Strip leading api.php if called directly
        if ($cleanPath === 'api.php' || $cleanPath === 'index.php') {
            $cleanPath = '';
        }

        $segments = $cleanPath !== '' ? explode('/', $cleanPath) : [];

        // Extract raw width and height from path or query
        $rawW = $query['w'] ?? null;
        $rawH = $query['h'] ?? null;
        $rawBg = $query['bg'] ?? null;
        $rawFg = $query['fg'] ?? null;

        if ($rawW === null && count($segments) >= 1) {
            // Support "600x400" shorthand in first segment as well as "/600/400"
            if (preg_match('/^(\d+)[xX](\d+)$/', $segments[0], $m)) {
                $rawW = $m[1];
                $rawH = $m[2];
                $rawBg = $rawBg ?? ($segments[1] ?? null);
                $rawFg = $rawFg ?? ($segments[2] ?? null);
            } else {
                $rawW = $segments[0];
                if (count($segments) === 1) {
                    $rawH = $rawW; // Square shortcut /{size}
                } elseif (count($segments) >= 2) {
                    // Check if segment[1] is a valid dimension integer (1..2048) or a hex color for square shortcut /{size}/{bg}/{fg}
                    if (ctype_digit($segments[1]) && (int)$segments[1] >= self::MIN_DIM && (int)$segments[1] <= self::MAX_DIM && count($segments) !== 3) {
                        $rawH = $segments[1];
                        $rawBg = $rawBg ?? ($segments[2] ?? null);
                        $rawFg = $rawFg ?? ($segments[3] ?? null);
                    } elseif (ctype_digit($segments[1]) && count($segments) === 3 && !preg_match('/^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $segments[2])) {
                        $rawH = $segments[1];
                        $rawBg = $rawBg ?? $segments[2];
                    } elseif (ctype_digit($segments[1]) && (int)$segments[1] <= self::MAX_DIM && (!preg_match('/^[0-9a-fA-F]{6}$/', $segments[1]) || count($segments) === 2 || count($segments) === 4)) {
                        $rawH = $segments[1];
                        $rawBg = $rawBg ?? ($segments[2] ?? null);
                        $rawFg = $rawFg ?? ($segments[3] ?? null);
                    } else {
                        // Square with custom colors: /{size}/{bg}/{fg}
                        $rawH = $rawW;
                        $rawBg = $rawBg ?? ($segments[1] ?? null);
                        $rawFg = $rawFg ?? ($segments[2] ?? null);
                    }
                }
            }
        }

        // Default if neither path nor query specified w/h
        if ($rawW === null) {
            $rawW = (string)self::DEFAULT_W;
            $rawH = (string)($rawH ?? self::DEFAULT_H);
        } elseif ($rawH === null) {
            $rawH = $rawW;
        }

        // Validate integer bounds (1 - 2048)
        if (!is_numeric($rawW) || !is_numeric($rawH) || strpos((string)$rawW, '.') !== false || strpos((string)$rawH, '.') !== false) {
            return [
                'status' => 400,
                'error' => 'Invalid dimensions: width and height must be integers between 1 and 2048.',
                'w' => 600,
                'h' => 400,
                'bg' => self::DEFAULT_BG,
                'fg' => self::DEFAULT_FG,
                'text' => '400 BAD REQUEST',
                'showDims' => false,
                'etag' => '"err-400"'
            ];
        }

        $w = (int)$rawW;
        $h = (int)$rawH;

        if ($w < self::MIN_DIM || $w > self::MAX_DIM || $h < self::MIN_DIM || $h > self::MAX_DIM) {
            return [
                'status' => 400,
                'error' => sprintf('Dimensions out of range: width (%d) and height (%d) must be between %d and %d.', $w, $h, self::MIN_DIM, self::MAX_DIM),
                'w' => 600,
                'h' => 400,
                'bg' => self::DEFAULT_BG,
                'fg' => self::DEFAULT_FG,
                'text' => '400 OUT OF RANGE',
                'showDims' => false,
                'etag' => '"err-range"'
            ];
        }

        $bg = self::sanitizeHex($rawBg, self::DEFAULT_BG);
        $fg = self::sanitizeHex($rawFg, self::DEFAULT_FG);

        $rawText = isset($query['text']) ? trim((string)$query['text']) : '';
        if ($rawText === '') {
            $rawText = self::DEFAULT_TEXT;
        } else {
            // Cap custom text at 64 chars to keep rendering crisp and prevent abuse
            $rawText = mb_substr($rawText, 0, 64, 'UTF-8');
        }

        $safeText = htmlspecialchars($rawText, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $showDims = false;
        if (isset($query['dims']) || isset($query['sub'])) {
            $val = (string)($query['dims'] ?? $query['sub']);
            $showDims = in_array(strtolower($val), ['1', 'true', 'yes', 'on'], true);
        }

        $etagHash = substr(md5("{$w}:{$h}:{$bg}:{$fg}:{$safeText}:" . ($showDims ? '1' : '0')), 0, 16);

        return [
            'status' => 200,
            'w' => $w,
            'h' => $h,
            'bg' => $bg,
            'fg' => $fg,
            'text' => $safeText,
            'showDims' => $showDims,
            'etag' => '"' . $etagHash . '"'
        ];
    }

    /**
     * Validates and normalizes a 3- or 6-character hex string (without leading #).
     */
    public static function sanitizeHex(mixed $input, string $fallback): string
    {
        if (!is_string($input) || $input === '') {
            return $fallback;
        }
        $clean = ltrim(trim($input), '#');
        if (preg_match('/^[0-9a-fA-F]{3}$/', $clean)) {
            $clean = $clean[0] . $clean[0] . $clean[1] . $clean[1] . $clean[2] . $clean[2];
            return strtoupper($clean);
        }
        if (preg_match('/^[0-9a-fA-F]{6}$/', $clean)) {
            return strtoupper($clean);
        }
        return $fallback;
    }

    /**
     * Renders the deterministic zero-disk SVG string matching grafik.png.
     *
     * @param array{w: int, h: int, bg: string, fg: string, text: string, showDims: bool} $params
     */
    public static function renderSvg(array $params): string
    {
        $w = $params['w'];
        $h = $params['h'];
        $bg = '#' . $params['bg'];
        $fg = '#' . $params['fg'];
        $text = $params['text'];
        $showDims = $params['showDims'];

        // Calculate proportional stroke width and responsive font size
        $minDim = min($w, $h);
        $strokeWidth = max(1.5, min(6.0, round($minDim * 0.0055, 2)));

        // Calculate text length before XML entities for optical sizing
        $decodedLen = max(1, mb_strlen(html_entity_decode($text, ENT_XML1 | ENT_QUOTES, 'UTF-8'), 'UTF-8'));
        $maxTextWidth = $w * 0.80;
        $baseFontSize = $minDim * 0.095;
        $widthBoundFontSize = $maxTextWidth / max(4, $decodedLen * 0.66);
        $fontSize = (int)max(8, min(96, round(min($baseFontSize, $widthBoundFontSize))));

        // Calculate diagonal crosshair lines from corners towards center with a clean gap around the text
        // In grafik.png, the four corner lines extend from (0,0)->(x1,y1), (w,0)->(x2,y1), (0,h)->(x1,y2), (w,h)->(x2,y2)
        // stopping neatly above and below the centered label band.
        $gapFraction = $showDims ? 0.22 : 0.17;
        $stopRatio = max(0.20, 0.50 - $gapFraction);

        $xLeft = round($w * $stopRatio, 2);
        $xRight = round($w * (1.0 - $stopRatio), 2);
        $yTop = round($h * $stopRatio, 2);
        $yBottom = round($h * (1.0 - $stopRatio), 2);

        $textY = $showDims ? round($h * 0.47, 2) : round($h * 0.50, 2);
        $subFontSize = (int)max(8, round($fontSize * 0.42));
        $subY = round($textY + ($fontSize * 0.72), 2);
        $letterSpacing = round($fontSize * 0.06, 2);

        $subtitleSvg = '';
        if ($showDims && $minDim >= 48) {
            $subtitleSvg = sprintf(
                "\n  <text x=\"50%%\" y=\"%s\" fill=\"%s\" fill-opacity=\"0.8\" font-family=\"'JetBrains Mono', ui-monospace, monospace\" font-size=\"%d\" font-weight=\"500\" text-anchor=\"middle\" dominant-baseline=\"central\">%d × %d</text>",
                $subY,
                $fg,
                $subFontSize,
                $w,
                $h
            );
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" role="img" aria-label="{$text} ({$w}x{$h})">
  <rect width="100%" height="100%" fill="{$bg}"/>
  <g stroke="{$fg}" stroke-width="{$strokeWidth}" stroke-linecap="round">
    <line x1="0" y1="0" x2="{$xLeft}" y2="{$yTop}"/>
    <line x1="{$w}" y1="0" x2="{$xRight}" y2="{$yTop}"/>
    <line x1="0" y1="{$h}" x2="{$xLeft}" y2="{$yBottom}"/>
    <line x1="{$w}" y1="{$h}" x2="{$xRight}" y2="{$yBottom}"/>
  </g>
  <text x="50%" y="{$textY}" fill="{$fg}" font-family="'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="{$fontSize}" font-weight="700" letter-spacing="{$letterSpacing}px" text-anchor="middle" dominant-baseline="central">{$text}</text>{$subtitleSvg}
</svg>
SVG;
    }
}

// Execute directly when invoked as an HTTP endpoint (and not included by unit tests)
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__ || defined('PLACEHOLGER_EXECUTE_API')) {
    $reqUri = $_SERVER['REQUEST_URI'] ?? '/';
    $params = SvgGenerator::parseRequest($reqUri, $_GET);

    // Common security, CORS, and strict non-indexing headers for all generated images
    header('Content-Type: image/svg+xml; charset=utf-8');
    header('X-Robots-Tag: noindex, noimageindex, noarchive, nosnippet');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
    header('Cross-Origin-Resource-Policy: cross-origin');
    header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'");
    header('X-Content-Type-Options: nosniff');

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }

    if ($params['status'] !== 200) {
        http_response_code($params['status']);
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo SvgGenerator::renderSvg($params);
        exit;
    }

    // ETag conditional request handling (304 Not Modified)
    $ifNoneMatch = trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '');
    header('ETag: ' . $params['etag']);
    header('Cache-Control: public, max-age=31536000, s-maxage=31536000, immutable');

    if ($ifNoneMatch !== '' && ($ifNoneMatch === $params['etag'] || $ifNoneMatch === 'W/' . $params['etag'])) {
        http_response_code(304);
        exit;
    }

    http_response_code(200);
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') {
        exit;
    }

    echo SvgGenerator::renderSvg($params);
}
