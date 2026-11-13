/**
 * End-to-End HTTP, SEO, Non-Indexing, Agentic & Client-Side JS SDK Test Suite
 * Boots PHP built-in server against ./dist/ and verifies all contracts.
 */
import { spawn } from 'node:child_process';
import assert from 'node:assert/strict';
import { createRequire } from 'node:module';

const require = createRequire(import.meta.url);
const PlaceholgerSDK = require('../../dist/placeholger.js');

const PORT = 18089;
const BASE = `http://127.0.0.1:${PORT}`;

async function waitForServer(url, retries = 30) {
  for (let i = 0; i < retries; i++) {
    try {
      const res = await fetch(url);
      if (res.ok) return;
    } catch (_) {}
    await new Promise((r) => setTimeout(r, 100));
  }
  throw new Error('PHP server failed to start in time');
}

async function runTests() {
  console.log('==> Starting PHP test server against ./dist on port ' + PORT + '...');
  const server = spawn('php', ['-S', `127.0.0.1:${PORT}`, '-t', 'dist', 'dist/index.php'], {
    stdio: 'ignore'
  });

  let passed = 0;
  function ok(cond, msg) {
    assert.ok(cond, msg);
    passed++;
    console.log('  ✓ ' + msg);
  }

  try {
    await waitForServer(`${BASE}/`);

    console.log('==> [1/4] Testing Landing Page SEO, Indexing & Performance Profile...');
    const homeRes = await fetch(`${BASE}/`);
    const homeHtml = await homeRes.text();
    ok(homeRes.status === 200, 'Landing page GET / returns 200 OK');
    ok((homeRes.headers.get('x-robots-tag') || '').includes('index, follow'), 'Landing page emits X-Robots-Tag: index, follow');
    ok(homeHtml.includes('<title>placeholger.hellinger.wtf'), 'Landing page contains SEO <title>');
    ok(homeHtml.includes('application/ld+json'), 'Landing page embeds Schema.org JSON-LD structured data');
    ok(homeHtml.includes('href="/llms.txt"') && homeHtml.includes('href="/openapi.json"'), 'Landing page links to /llms.txt and /openapi.json');
    ok(!homeHtml.includes('cdn.tailwindcss.com'), 'Landing page uses compiled CSS with zero render-blocking Tailwind CDN script');

    console.log('==> [2/4] Testing SVG Endpoints, Caching, ETag & Strict Non-Indexing Headers...');
    const svgRes = await fetch(`${BASE}/600/400/0f172a/38bdf8?text=Agent+Test&dims=1`);
    const svgBody = await svgRes.text();
    ok(svgRes.status === 200, 'GET /600/400/0f172a/38bdf8?text=Agent+Test&dims=1 returns 200 OK');
    ok((svgRes.headers.get('content-type') || '').includes('image/svg+xml'), 'SVG response has Content-Type: image/svg+xml');

    const robotsHeader = svgRes.headers.get('x-robots-tag') || '';
    ok(robotsHeader.includes('noindex') && robotsHeader.includes('noimageindex'), 'SVG response strictly enforces X-Robots-Tag: noindex, noimageindex');

    const cacheHeader = svgRes.headers.get('cache-control') || '';
    ok(cacheHeader.includes('max-age=31536000') && cacheHeader.includes('immutable'), 'SVG response enforces immutable edge caching');

    const etag = svgRes.headers.get('etag');
    ok(Boolean(etag), 'SVG response includes deterministic ETag header');

    // Conditional request 304 check
    const cachedRes = await fetch(`${BASE}/600/400/0f172a/38bdf8?text=Agent+Test&dims=1`, {
      headers: { 'If-None-Match': etag }
    });
    ok(cachedRes.status === 304, 'Conditional request with matching If-None-Match returns 304 Not Modified');

    // Out of bounds 400 check
    const badRes = await fetch(`${BASE}/4096/400`);
    ok(badRes.status === 400, 'Out-of-bounds dimension (/4096/400) returns HTTP 400 Bad Request');

    console.log('==> [3/4] Testing Agentic Browsing & Discovery Endpoints...');
    const llmsRes = await fetch(`${BASE}/llms.txt`);
    const llmsText = await llmsRes.text();
    ok(llmsRes.status === 200 && llmsText.includes('placeholger.hellinger.wtf'), 'GET /llms.txt returns 200 OK with Markdown instructions');

    const oaRes = await fetch(`${BASE}/openapi.json`);
    const oaJson = await oaRes.json();
    ok(oaRes.status === 200 && oaJson.openapi === '3.1.0', 'GET /openapi.json returns valid OpenAPI 3.1.0 JSON');

    const robotsTxtRes = await fetch(`${BASE}/robots.txt`);
    const robotsTxt = await robotsTxtRes.text();
    ok(robotsTxtRes.status === 200 && robotsTxt.includes('Disallow: /api.php'), 'GET /robots.txt protects crawl budget from dynamic image paths');

    console.log('==> [4/4] Testing Client-Side On-The-Fly JS SDK (placeholger.js)...');
    const parsed = PlaceholgerSDK.parseSpec('800/600/18181b/22c55e?text=Client+SDK&dims=1');
    ok(parsed.w === 800 && parsed.h === 600 && parsed.bg === '18181B' && parsed.showDims === true, 'Placeholger.parseSpec() parses shorthand specs accurately');

    const clientSvg = PlaceholgerSDK.svg(parsed);
    ok(clientSvg.includes('width="800"') && clientSvg.includes('>Client SDK</text>') && clientSvg.includes('>800 × 600</text>'), 'Placeholger.svg() generates identical SVG on the fly in JS');

    const dataUri = PlaceholgerSDK.dataUri('512/1e3a8a/93c5fd?text=Square');
    ok(dataUri.startsWith('data:image/svg+xml;utf8,') && decodeURIComponent(dataUri).includes('width="512" height="512"'), 'Placeholger.dataUri() generates valid data URI');

    console.log('==> [5/5] Testing Self-Host Only Policy & Hotlink Rejection for placeholger.js...');
    ok(!homeHtml.includes('https://placeholger.hellinger.wtf/placeholger.js"'), 'Landing page does not offer remote <script src="https://.../placeholger.js"> loading');
    ok(homeHtml.includes('src="/js/placeholger.js"'), 'Landing page documents self-hosted /js/placeholger.js');

    const badRefRes = await fetch(`${BASE}/placeholger.js`, {
      headers: { Referer: 'https://evil-third-party.example/index.html' }
    });
    ok(badRefRes.status === 403, 'GET /placeholger.js with remote Referer is rejected with 403 Forbidden');

    const crossSiteRes = await fetch(`${BASE}/placeholger.js`, {
      headers: { 'Sec-Fetch-Site': 'cross-site' }
    });
    ok(crossSiteRes.status === 403, 'GET /placeholger.js with Sec-Fetch-Site: cross-site is rejected with 403 Forbidden');

    const sameOriginRes = await fetch(`${BASE}/placeholger.js`, {
      headers: { Referer: `${BASE}/`, 'Sec-Fetch-Site': 'same-origin' }
    });
    ok(sameOriginRes.status === 200, 'GET /placeholger.js with same-origin Referer returns 200 OK');
    ok(sameOriginRes.headers.get('cross-origin-resource-policy') === 'same-origin', 'placeholger.js emits Cross-Origin-Resource-Policy: same-origin');
    ok(!sameOriginRes.headers.get('access-control-allow-origin'), 'placeholger.js does not emit wildcard Access-Control-Allow-Origin');

    const directDownloadRes = await fetch(`${BASE}/placeholger.js`);
    ok(directDownloadRes.status === 200, 'Direct download of /placeholger.js (no Referer/cross-site) returns 200 OK for self-hosting');

    console.log(`\n✅ All ${passed} E2E, SEO, Agentic & JS SDK assertions passed!`);
  } finally {
    server.kill();
  }
}

runTests().catch((err) => {
  console.error('❌ E2E Test Failure:', err);
  process.exit(1);
});
