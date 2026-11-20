<?php
declare(strict_types=1);

/**
 * Dedicated Server-Side PHP Security Test Suite for Placeholger\SvgGenerator
 *
 * Enforces zero PHP notices/warnings/deprecations via strict error handler and
 * verifies resistance against array injection, type confusion, numeric bypasses,
 * XML 1.0 control-character/UTF-8 corruption, XSS/XXE/CDATA breakout, direct
 * renderSvg() misuse, and path traversal / excessive segment payloads.
 */

set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

require_once __DIR__ . '/../../src/public/api.php';

use Placeholger\SvgGenerator;

$passed = 0;
$failed = 0;

function assertSec(bool $condition, string $message): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  ✓ {$message}\n";
    } else {
        $failed++;
        fwrite(STDERR, "  ✗ FAILED: {$message}\n");
    }
}

/**
 * Parses SVG XML strictly and verifies only allowed wireframe elements and safe attributes exist.
 */
function assertSafeSvgXml(string $svg, string $context): void
{
    $prevUseErrors = libxml_use_internal_errors(true);
    libxml_clear_errors();
    $xml = simplexml_load_string($svg, SimpleXMLElement::class, LIBXML_NONET);
    $errors = libxml_get_errors();
    libxml_use_internal_errors($prevUseErrors);

    assertSec($xml !== false && $errors === [], "{$context}: SVG is strictly well-formed XML 1.0");
    if ($xml === false) {
        return;
    }

    $allowedTags = ['svg', 'title', 'rect', 'g', 'line', 'text'];
    $allowedAttrs = [
        'xmlns', 'width', 'height', 'viewbox', 'role', 'aria-label', 'aria-hidden',
        'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'x1', 'y1', 'x2', 'y2',
        'x', 'y', 'font-family', 'font-size', 'font-weight', 'letter-spacing',
        'text-anchor', 'dominant-baseline',
    ];
    $dom = dom_import_simplexml($xml)->ownerDocument;
    $allElements = $dom->getElementsByTagName('*');
    $onlyAllowedTags = true;
    $hasDangerousAttr = false;

    foreach ($allElements as $el) {
        if (!in_array($el->nodeName, $allowedTags, true)) {
            $onlyAllowedTags = false;
        }
        foreach ($el->attributes as $attr) {
            $name = strtolower($attr->nodeName);
            $val = strtolower($attr->nodeValue);
            if (
                !in_array($name, $allowedAttrs, true) ||
                str_starts_with($name, 'on') ||
                (in_array($name, ['href', 'xlink:href', 'src', 'action'], true) && (str_contains($val, 'javascript:') || str_contains($val, 'data:text/html')))
            ) {
                $hasDangerousAttr = true;
            }
        }
    }

    assertSec($onlyAllowedTags, "{$context}: SVG contains only allowlisted wireframe elements");
    assertSec(!$hasDangerousAttr, "{$context}: SVG contains zero event-handler or script attributes");
}

echo "==> Running PHP Server-Side Security Test Suite...\n";

// 1. Array query parameter injection & type confusion (must emit zero warnings)
$arrayCases = [
    '/?w[]=600' => ['/', ['w' => ['600']], 400],
    '/?h[]=400' => ['/', ['h' => ['400']], 400],
    '/?w=600&h[]=400' => ['/', ['w' => '600', 'h' => ['400']], 400],
    '/?w[][]=100' => ['/', ['w' => [['100']]], 400],
    '/600/400?bg[]=000' => ['/600/400', ['bg' => ['000']], 200],
    '/600/400?fg[]=fff' => ['/600/400', ['fg' => ['fff']], 200],
    '/600/400?text[]=evil' => ['/600/400', ['text' => ['evil']], 200],
    '/600/400?dims[]=1' => ['/600/400', ['dims' => ['1']], 200],
    '/600/400?sub[]=1' => ['/600/400', ['sub' => ['1']], 200],
    '/600/400?font[]=mono' => ['/600/400', ['font' => ['mono']], 200],
];

foreach ($arrayCases as $label => [$path, $query, $expectedStatus]) {
    $res = SvgGenerator::parseRequest($path, $query);
    assertSec($res['status'] === $expectedStatus, "Array input {$label} returns status {$expectedStatus} without warnings");
    $svg = SvgGenerator::renderSvg($res);
    assertSafeSvgXml($svg, "Array input {$label}");
    if ($expectedStatus === 200) {
        assertSec($res['text'] === SvgGenerator::DEFAULT_TEXT || $label !== '/600/400?text[]=evil', "Array text input does not stringify to 'Array'");
        assertSec($res['showDims'] === false, "Array dims/sub input does not enable showDims");
    }
}

// 2. Numeric validation bypass payloads
$numericBypasses = [
    ['1e2', '400', 'scientific notation 1e2'],
    ['6e2', '400', 'scientific notation 6e2'],
    ['999e999', '400', 'overflow scientific notation 999e999'],
    ['+600', '400', 'leading plus sign +600'],
    [' 600', '400', 'leading space'],
    ["600\n", '400', 'trailing newline'],
    ['0x100', '400', 'hex string 0x100'],
    ['600.0', '400', 'float string 600.0'],
    ['NaN', '400', 'NaN string'],
    ['INF', '400', 'INF string'],
    ['999999999999999999999999', '400', '64-bit integer overflow'],
    ['-999999999999999999999999', '400', 'negative 64-bit integer underflow'],
];

foreach ($numericBypasses as [$wVal, $hVal, $desc]) {
    $resQuery = SvgGenerator::parseRequest('/', ['w' => $wVal, 'h' => $hVal]);
    assertSec($resQuery['status'] === 400, "Rejects query {$desc} with 400 Bad Request");
    $resPath = SvgGenerator::parseRequest('/' . $wVal . '/' . $hVal, []);
    assertSec($resPath['status'] === 400, "Rejects path {$desc} with 400 Bad Request");
}

// 3. XML 1.0 control characters & invalid UTF-8 sequences
$controlPayload = "A\x00B\x01C\x07D\x0BE\x0CF\x1FG\x7FH\r\n\tI";
$resCtrl = SvgGenerator::parseRequest('/600/400', ['text' => $controlPayload]);
$svgCtrl = SvgGenerator::renderSvg($resCtrl);
assertSec(preg_match('/[\x00-\x1F\x7F]/', $resCtrl['text']) === 0, 'Strips all ASCII/XML 1.0 control characters from sanitized text');
assertSec(!preg_match('/[\x00-\x09\x0B-\x1F\x7F]/', $svgCtrl), 'Strips all disallowed XML 1.0 control characters from SVG output');
assertSafeSvgXml($svgCtrl, 'Control characters payload');

$invalidUtf8Payloads = [
    "\xFF\xFE\xFD",
    "Hello\xC3\x28World",
    "\x80\x81",
];
foreach ($invalidUtf8Payloads as $idx => $badUtf8) {
    $resUtf8 = SvgGenerator::parseRequest('/600/400', ['text' => $badUtf8]);
    $svgUtf8 = SvgGenerator::renderSvg($resUtf8);
    assertSec(mb_check_encoding($svgUtf8, 'UTF-8'), "Invalid UTF-8 payload #{$idx} produces valid UTF-8");
    assertSec($resUtf8['text'] !== '', "Invalid UTF-8 payload #{$idx} never collapses to empty string");
    assertSafeSvgXml($svgUtf8, "Invalid UTF-8 payload #{$idx}");
}

// 4. XSS, attribute breakout, CDATA breakout, and XXE injection payloads
$xssPayloads = [
    '"><svg onload=alert(1)>',
    "' onclick='alert(1)' x='",
    '</text><script>alert(1)</script><text>',
    '<![CDATA[<script>alert(1)</script>]]>',
    '<!ENTITY xxe SYSTEM "file:///etc/passwd">',
    '&xxe;&amp;<image href="javascript:alert(1)"/>',
];
foreach ($xssPayloads as $idx => $xss) {
    $resXss = SvgGenerator::parseRequest('/600/400', ['text' => $xss, 'bg' => $xss, 'fg' => $xss, 'font' => $xss]);
    $svgXss = SvgGenerator::renderSvg($resXss);
    assertSafeSvgXml($svgXss, "XSS/XXE payload #{$idx}");
    assertSec(!str_contains(strtolower($svgXss), '<script'), "XSS payload #{$idx} cannot inject <script> tag");
    assertSec(!str_contains(strtolower($svgXss), '<!entity'), "XXE payload #{$idx} cannot inject <!ENTITY");
}

// 5. Direct SvgGenerator::renderSvg() defense-in-depth with untrusted inputs
$directSvg = SvgGenerator::renderSvg([
    'w' => '<script>',
    'h' => 999999,
    'bg' => 'red" onload="alert(1)',
    'fg' => ['not-a-hex'],
    'text' => "\x00<script>alert('direct')</script>",
    'showDims' => ['evil'],
    'font' => "';alert(1);//",
]);
assertSec(str_contains($directSvg, 'width="600" height="2048"'), 'Direct renderSvg() clamps out-of-range and invalid dimensions');
assertSec(str_contains($directSvg, 'fill="#9E9EA0"') && str_contains($directSvg, 'stroke="#FFFFFF"'), 'Direct renderSvg() sanitizes untrusted bg/fg colors');
assertSafeSvgXml($directSvg, 'Direct renderSvg() untrusted params');

// 6. Path traversal, null bytes, and excessive path segments
$badPaths = [
    ['/.env', 'dotfile path'],
    ['/../.env', 'parent directory traversal'],
    ["/600/400\x00.php", 'null byte in path'],
    ['/600/400/9e9ea0/ffffff/extra', '5 segments'],
    ['/600/400/9e9ea0/ffffff/extra/junk', '6 segments'],
];
foreach ($badPaths as [$badPath, $desc]) {
    $resBadPath = SvgGenerator::parseRequest($badPath, []);
    assertSec($resBadPath['status'] === 400, "Rejects {$desc} ({$badPath}) with 400 Bad Request");
    assertSafeSvgXml(SvgGenerator::renderSvg($resBadPath), "Bad path {$desc}");
}

echo "\n==> PHP Security Tests Summary: {$passed} passed, {$failed} failed.\n";
if ($failed > 0) {
    exit(1);
}
