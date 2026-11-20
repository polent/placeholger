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
    public const DEFAULT_FONT = 'sans';
    public const FONTS = [
        'sans' => [
            'stack' => "'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            'widthFactor' => 0.66,
        ],
        'mono' => [
            'stack' => "'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace",
            'widthFactor' => 0.68,
        ],
        'serif' => [
            'stack' => "ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif",
            'widthFactor' => 0.62,
        ],
        'rounded' => [
            'stack' => "ui-rounded, 'SF Pro Rounded', 'Hiragino Maru Gothic ProN', Quicksand, sans-serif",
            'widthFactor' => 0.66,
        ],
        'condensed' => [
            'stack' => "'Arial Narrow', 'Roboto Condensed', 'Helvetica Neue Condensed', Impact, sans-serif",
            'widthFactor' => 0.56,
        ],
    ];

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
     *   font: string,
     *   etag: string
     * }
     */
    public static function parseRequest(string $requestPath, array $query): array
    {
        if (str_contains($requestPath, "\0")) {
            return [
                'status' => 400,
                'error' => 'Invalid path: null byte not permitted.',
                'w' => 600,
                'h' => 400,
                'bg' => self::DEFAULT_BG,
                'fg' => self::DEFAULT_FG,
                'text' => '400 BAD REQUEST',
                'showDims' => false,
                'font' => self::DEFAULT_FONT,
                'etag' => '"err-400"'
            ];
        }

        $parsedPath = parse_url($requestPath, PHP_URL_PATH);
        $cleanPath = is_string($parsedPath) ? trim($parsedPath, '/') : '';
        // Strip leading api.php if called directly
        if ($cleanPath === 'api.php' || $cleanPath === 'index.php') {
            $cleanPath = '';
        }

        $segments = $cleanPath !== '' ? explode('/', $cleanPath) : [];
        if (count($segments) > 4) {
            return [
                'status' => 400,
                'error' => 'Invalid path: too many segments.',
                'w' => 600,
                'h' => 400,
                'bg' => self::DEFAULT_BG,
                'fg' => self::DEFAULT_FG,
                'text' => '400 BAD REQUEST',
                'showDims' => false,
                'font' => self::DEFAULT_FONT,
                'etag' => '"err-400"'
            ];
        }

        // Extract raw width and height from path or query
        $rawW = $query['w'] ?? null;
        $rawH = $query['h'] ?? null;
        $rawBg = $query['bg'] ?? null;
        $rawFg = $query['fg'] ?? null;

        if ($rawW === null && count($segments) >= 1) {
            // Support "600x400" shorthand in first segment as well as "/600/400"
            if (preg_match('/^(\d+)[xX](\d+)\z/', $segments[0], $m)) {
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
                    } elseif (ctype_digit($segments[1]) && count($segments) === 3 && !preg_match('/^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?\z/', $segments[2])) {
                        $rawH = $segments[1];
                        $rawBg = $rawBg ?? $segments[2];
                    } elseif (ctype_digit($segments[1]) && (int)$segments[1] <= self::MAX_DIM && (!preg_match('/^[0-9a-fA-F]{6}\z/', $segments[1]) || count($segments) === 2 || count($segments) === 4)) {
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

        // Default if neither path nor query specified w/h (keep $rawH un-cast to avoid Array-to-string warnings)
        if ($rawW === null) {
            $rawW = (string)self::DEFAULT_W;
            if ($rawH === null) {
                $rawH = (string)self::DEFAULT_H;
            }
        } elseif ($rawH === null) {
            $rawH = $rawW;
        }

        // Strictly validate integer inputs (reject arrays, scientific notation, whitespace, signs, floats)
        $isValidIntInput = static fn(mixed $v): bool => is_int($v) || (is_string($v) && preg_match('/^-?\d+\z/', $v) === 1);
        if (!$isValidIntInput($rawW) || !$isValidIntInput($rawH)) {
            return [
                'status' => 400,
                'error' => 'Invalid dimensions: width and height must be integers between 1 and 2048.',
                'w' => 600,
                'h' => 400,
                'bg' => self::DEFAULT_BG,
                'fg' => self::DEFAULT_FG,
                'text' => '400 BAD REQUEST',
                'showDims' => false,
                'font' => self::DEFAULT_FONT,
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
                'font' => self::DEFAULT_FONT,
                'etag' => '"err-range"'
            ];
        }

        $bg = self::sanitizeHex($rawBg, self::DEFAULT_BG);
        $fg = self::sanitizeHex($rawFg, self::DEFAULT_FG);
        $safeText = self::sanitizeText($query['text'] ?? null);

        $showDims = false;
        $rawDims = $query['dims'] ?? $query['sub'] ?? null;
        if (is_bool($rawDims)) {
            $showDims = $rawDims;
        } elseif (is_int($rawDims) || is_string($rawDims)) {
            $showDims = in_array(strtolower(trim((string)$rawDims)), ['1', 'true', 'yes', 'on'], true);
        }

        $font = self::sanitizeFont($query['font'] ?? null);
        $etagHash = substr(md5("{$w}:{$h}:{$bg}:{$fg}:{$safeText}:" . ($showDims ? '1' : '0') . ":{$font}"), 0, 16);

        return [
            'status' => 200,
            'w' => $w,
            'h' => $h,
            'bg' => $bg,
            'fg' => $fg,
            'text' => $safeText,
            'showDims' => $showDims,
            'font' => $font,
            'etag' => '"' . $etagHash . '"'
        ];
    }

    /**
     * Scrubs invalid UTF-8 bytes and XML 1.0 control characters, caps at 64 chars, and escapes XML entities.
     */
    public static function sanitizeText(mixed $input): string
    {
        if (!is_string($input)) {
            return self::DEFAULT_TEXT;
        }

        $scrubbed = mb_scrub($input, 'UTF-8');
        $clean = (string)preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $scrubbed);
        $clean = trim($clean);
        if ($clean === '') {
            return self::DEFAULT_TEXT;
        }

        $capped = mb_substr($clean, 0, 64, 'UTF-8');
        return htmlspecialchars($capped, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Validates and normalizes a font preset key.
     */
    public static function sanitizeFont(mixed $input): string
    {
        if (!is_string($input) || $input === '') {
            return self::DEFAULT_FONT;
        }
        $clean = strtolower(trim($input));
        return isset(self::FONTS[$clean]) ? $clean : self::DEFAULT_FONT;
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
        if (preg_match('/^[0-9a-fA-F]{3}\z/', $clean)) {
            $clean = $clean[0] . $clean[0] . $clean[1] . $clean[1] . $clean[2] . $clean[2];
            return strtoupper($clean);
        }
        if (preg_match('/^[0-9a-fA-F]{6}\z/', $clean)) {
            return strtoupper($clean);
        }
        return $fallback;
    }

    /**
     * Renders the deterministic zero-disk SVG string matching grafik.png.
     *
     * @param array{w?: mixed, h?: mixed, bg?: mixed, fg?: mixed, text?: mixed, showDims?: mixed, font?: mixed} $params
     */
    public static function renderSvg(array $params): string
    {
        $rawW = $params['w'] ?? self::DEFAULT_W;
        $rawH = $params['h'] ?? self::DEFAULT_H;
        $w = (is_int($rawW) || (is_string($rawW) && preg_match('/^\d+\z/', $rawW) === 1))
            ? max(self::MIN_DIM, min(self::MAX_DIM, (int)$rawW))
            : self::DEFAULT_W;
        $h = (is_int($rawH) || (is_string($rawH) && preg_match('/^\d+\z/', $rawH) === 1))
            ? max(self::MIN_DIM, min(self::MAX_DIM, (int)$rawH))
            : self::DEFAULT_H;
        $bg = '#' . self::sanitizeHex($params['bg'] ?? null, self::DEFAULT_BG);
        $fg = '#' . self::sanitizeHex($params['fg'] ?? null, self::DEFAULT_FG);

        $rawText = $params['text'] ?? null;
        if (
            !is_string($rawText) ||
            trim($rawText) === '' ||
            !mb_check_encoding($rawText, 'UTF-8') ||
            preg_match('/[<>"\'\x00-\x1F\x7F]|&(?!(?:amp|lt|gt|quot|apos);)/u', $rawText) === 1
        ) {
            $text = self::sanitizeText($rawText);
        } else {
            $text = $rawText;
        }

        $showDims = !empty($params['showDims']);
        $fontKey = self::sanitizeFont($params['font'] ?? self::DEFAULT_FONT);
        $fontConfig = self::FONTS[$fontKey];

        // Calculate proportional stroke width and responsive font size
        $minDim = min($w, $h);
        $strokeWidth = max(1.5, min(6.0, round($minDim * 0.0055, 2)));

        // Calculate text length before XML entities for optical sizing
        $decodedLen = max(1, mb_strlen(html_entity_decode($text, ENT_XML1 | ENT_QUOTES, 'UTF-8'), 'UTF-8'));
        $maxTextWidth = $w * 0.80;
        $baseFontSize = $minDim * 0.095;
        $widthBoundFontSize = $maxTextWidth / max(4, $decodedLen * $fontConfig['widthFactor']);
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
                "\n  <text aria-hidden=\"true\" x=\"50%%\" y=\"%s\" fill=\"%s\" font-family=\"'JetBrains Mono', ui-monospace, monospace\" font-size=\"%d\" font-weight=\"500\" text-anchor=\"middle\" dominant-baseline=\"central\">%d × %d</text>",
                $subY,
                $fg,
                $subFontSize,
                $w,
                $h
            );
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}" role="img" aria-label="{$text} ({$w}x{$h})">
  <title>{$text} ({$w}x{$h})</title>
  <rect width="100%" height="100%" fill="{$bg}"/>
  <g aria-hidden="true" stroke="{$fg}" stroke-width="{$strokeWidth}" stroke-linecap="round">
    <line x1="0" y1="0" x2="{$xLeft}" y2="{$yTop}"/>
    <line x1="{$w}" y1="0" x2="{$xRight}" y2="{$yTop}"/>
    <line x1="0" y1="{$h}" x2="{$xLeft}" y2="{$yBottom}"/>
    <line x1="{$w}" y1="{$h}" x2="{$xRight}" y2="{$yBottom}"/>
  </g>
  <text aria-hidden="true" x="50%" y="{$textY}" fill="{$fg}" font-family="{$fontConfig['stack']}" font-size="{$fontSize}" font-weight="700" letter-spacing="{$letterSpacing}px" text-anchor="middle" dominant-baseline="central">{$text}</text>{$subtitleSvg}
</svg>
SVG;
    }
}

// Execute directly when invoked as an HTTP endpoint (and not included by unit tests)
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__ || defined('PLACEHOLGER_EXECUTE_API')) {
    $reqUri = is_string($_SERVER['REQUEST_URI'] ?? null) ? $_SERVER['REQUEST_URI'] : '/';
    $reqMethod = is_string($_SERVER['REQUEST_METHOD'] ?? null) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    $params = SvgGenerator::parseRequest($reqUri, $_GET);

    // Common security, CORS, and strict non-indexing headers for all generated images
    header('Content-Type: image/svg+xml; charset=utf-8');
    header('X-Robots-Tag: noindex, noimageindex, noarchive, nosnippet');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
    header('Cross-Origin-Resource-Policy: cross-origin');
    header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'");
    header('X-Content-Type-Options: nosniff');

    if ($reqMethod === 'OPTIONS') {
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
    $rawIfNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
    $ifNoneMatch = is_string($rawIfNoneMatch) ? trim($rawIfNoneMatch) : '';
    header('ETag: ' . $params['etag']);
    header('Cache-Control: public, max-age=31536000, s-maxage=31536000, immutable');

    if ($ifNoneMatch !== '' && ($ifNoneMatch === $params['etag'] || $ifNoneMatch === 'W/' . $params['etag'])) {
        http_response_code(304);
        exit;
    }

    http_response_code(200);
    if ($reqMethod === 'HEAD') {
        exit;
    }

    echo SvgGenerator::renderSvg($params);
}
