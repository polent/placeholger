<?php
declare(strict_types=1);

/**
 * Automated Unit & Contract Test Suite for Placeholger\SvgGenerator
 */

require_once __DIR__ . '/../../src/public/api.php';

use Placeholger\SvgGenerator;

$passed = 0;
$failed = 0;

function assertTrue(bool $condition, string $message): void
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

echo "==> Running PHP SvgGenerator Unit & Contract Tests...\n";

// 1. Default route /600/400
$res1 = SvgGenerator::parseRequest('/600/400', []);
assertTrue($res1['status'] === 200, 'Default /600/400 returns 200 OK');
assertTrue($res1['w'] === 600 && $res1['h'] === 400, 'Default /600/400 parses width=600, height=400');
assertTrue($res1['bg'] === '9E9EA0' && $res1['fg'] === 'FFFFFF', 'Default colors are #9E9EA0 and #FFFFFF');
assertTrue($res1['text'] === 'PLACEHOLGER', 'Default text is PLACEHOLGER');

// 2. Square shortcut /512
$res2 = SvgGenerator::parseRequest('/512', []);
assertTrue($res2['status'] === 200 && $res2['w'] === 512 && $res2['h'] === 512, 'Square route /512 sets w=512, h=512');

// 3. Full path with custom colors & query text + dims
$res3 = SvgGenerator::parseRequest('/1200/630/0f172a/38bdf8', ['text' => 'Hero Banner', 'dims' => '1']);
assertTrue($res3['status'] === 200, 'Custom path /1200/630/0f172a/38bdf8 returns 200');
assertTrue($res3['bg'] === '0F172A' && $res3['fg'] === '38BDF8', 'Normalizes hex colors to uppercase 6-char');
assertTrue($res3['text'] === 'Hero Banner' && $res3['showDims'] === true, 'Extracts custom text and dims=1');

// 4. 3-character hex expansion
$res4 = SvgGenerator::parseRequest('/300/200/0a0/fff', []);
assertTrue($res4['bg'] === '00AA00' && $res4['fg'] === 'FFFFFF', 'Expands 3-char hex #0a0 -> #00AA00 and #fff -> #FFFFFF');

// 5. Shorthand 600x400 syntax
$res5 = SvgGenerator::parseRequest('/800x600/18181b/22c55e', []);
assertTrue($res5['w'] === 800 && $res5['h'] === 600 && $res5['bg'] === '18181B', 'Supports /800x600/bg/fg syntax');

// 6. XML / XSS Sanitization on custom text
$res6 = SvgGenerator::parseRequest('/600/400', ['text' => '<script>alert("xss")&</script>']);
assertTrue(strpos($res6['text'], '<script>') === false, 'Prevents raw <script> tags in text');
assertTrue(strpos($res6['text'], '&lt;script&gt;') !== false && strpos($res6['text'], '&amp;') !== false, 'Properly encodes XML entities');

// 7. Boundary Validation (1 - 2048)
$resZero = SvgGenerator::parseRequest('/0/400', []);
assertTrue($resZero['status'] === 400, 'Rejects width=0 with 400 Bad Request');

$resNegative = SvgGenerator::parseRequest('/-100/400', []);
assertTrue($resNegative['status'] === 400, 'Rejects negative width with 400 Bad Request');

$resTooLarge = SvgGenerator::parseRequest('/2049/400', []);
assertTrue($resTooLarge['status'] === 400, 'Rejects width=2049 (> 2048) with 400 Bad Request');

$resNonNumeric = SvgGenerator::parseRequest('/invalid/dimensions', []);
assertTrue($resNonNumeric['status'] === 400, 'Rejects non-numeric path with 400 Bad Request');

// 8. SVG Rendering & grafik.png Wireframe Structure
$svg = SvgGenerator::renderSvg($res3);
assertTrue(strpos($svg, '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630"') !== false, 'Renders valid root <svg> element with dimensions');
assertTrue(strpos($svg, 'fill="#0F172A"') !== false && strpos($svg, 'stroke="#38BDF8"') !== false, 'Applies custom background and foreground colors in SVG');
assertTrue(strpos($svg, '>Hero Banner</text>') !== false, 'Renders custom centered label');
assertTrue(strpos($svg, '>1200 × 630</text>') !== false, 'Renders dimensions subtitle when showDims=true');
assertTrue(substr_count($svg, '<line ') === 4, 'Renders 4 corner-to-center diagonal crosshair lines with center gap matching grafik.png');

// Verify XML well-formedness via SimpleXML
$xml = @simplexml_load_string($svg);
assertTrue($xml !== false, 'Generated SVG is strictly well-formed XML');

echo "\n==> PHP Unit Tests Summary: {$passed} passed, {$failed} failed.\n";
if ($failed > 0) {
    exit(1);
}
