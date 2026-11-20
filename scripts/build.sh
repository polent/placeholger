#!/usr/bin/env bash
# Builds the production dist/ directory from src/ (Zero external network dependencies required)
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "==> [0/4] Syncing release version & cachebusting query parameters..."
node -e '
const fs = require("fs");
const { execSync } = require("child_process");

const pkgPath = "./package.json";
const pkg = JSON.parse(fs.readFileSync(pkgPath, "utf8"));

let headVersion = null;
try {
  const headPkg = JSON.parse(execSync("git show HEAD:package.json", { encoding: "utf8", stdio: ["ignore", "pipe", "ignore"] }));
  headVersion = headPkg.version || null;
} catch (_) {}

if (process.env.SKIP_VERSION_BUMP !== "1" && (process.env.FORCE_BUMP === "1" || (headVersion && pkg.version === headVersion))) {
  const parts = String(pkg.version).split(".").map((n) => parseInt(n, 10) || 0);
  const bumpType = process.env.BUMP || "patch";
  if (bumpType === "major") {
    parts[0] += 1; parts[1] = 0; parts[2] = 0;
  } else if (bumpType === "minor") {
    parts[1] += 1; parts[2] = 0;
  } else {
    parts[2] += 1;
  }
  pkg.version = parts.join(".");
  fs.writeFileSync(pkgPath, JSON.stringify(pkg, null, 2) + "\n");
  console.log("  ✓ Bumped version: " + headVersion + " -> " + pkg.version);
} else {
  console.log("  ✓ Using version: " + pkg.version);
}

const v = pkg.version;

function updateFile(filePath, transform) {
  const orig = fs.readFileSync(filePath, "utf8");
  const next = transform(orig);
  if (next !== orig) {
    fs.writeFileSync(filePath, next);
  }
}

updateFile("./src/public/placeholger.js", (s) =>
  s.replace(/(version:\s*\x27)[^\x27]+(\x27)/, "$1" + v + "$2")
);
updateFile("./src/public/openapi.json", (s) =>
  s.replace(/("info"\s*:\s*\{[\s\S]*?"version"\s*:\s*")[^"]+(")/, "$1" + v + "$2")
);
updateFile("./src/public/.well-known/ai-catalog.json", (s) =>
  s.replace(/("version"\s*:\s*")[^"]+(")/, "$1" + v + "$2")
);
updateFile("./src/views/landing.php", (s) =>
  s
    .replace(/(rounded bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">)v[0-9]+\.[0-9]+(?:\.[0-9]+)?(<\/span>)/, "$1v" + v + "$2")
    .replace(/href="\/assets\/app\.css(?:\?v=[^"]*)?"/, "href=\"/assets/app.css?v=" + v + "\"")
    .replace(/href="\/placeholger\.js(?:\?v=[^"]*)?"/, "href=\"/placeholger.js?v=" + v + "\"")
    .replace(/src="\/placeholger\.js(?:\?v=[^"]*)?"/, "src=\"/placeholger.js?v=" + v + "\"")
    .replace(/src="\/assets\/app\.js(?:\?v=[^"]*)?"/, "src=\"/assets/app.js?v=" + v + "\"")
);
'

echo "==> [1/4] Cleaning dist/ directory..."
rm -rf dist
mkdir -p dist/assets dist/views dist/.well-known

echo "==> [2/4] Copying PHP, Apache .htaccess, JS SDK, SEO, and Agentic files..."
cp src/public/.htaccess dist/.htaccess
cp src/public/index.php dist/index.php
cp src/public/api.php dist/api.php
node scripts/minify-js.mjs src/public/placeholger.js dist/placeholger.js
node scripts/minify-js.mjs src/public/assets/app.js dist/assets/app.js
node --check dist/placeholger.js
node --check dist/assets/app.js
cp src/public/robots.txt dist/robots.txt
cp src/public/sitemap.xml dist/sitemap.xml
cp src/public/llms.txt dist/llms.txt
cp src/public/llms-full.txt dist/llms-full.txt
cp src/public/openapi.json dist/openapi.json
cp src/public/.well-known/ai-plugin.json dist/.well-known/ai-plugin.json
cp src/public/.well-known/ai-catalog.json dist/.well-known/ai-catalog.json
cp src/views/landing.php dist/views/landing.php

echo "==> [3/4] Compiling & minifying Neutral Spec CSS..."
node -e '
const fs = require("fs");
const css = fs.readFileSync("./src/assets/tailwind.css", "utf8");
const minified = css
  .replace(/\/\*[\s\S]*?\*\//g, "")
  .replace(/\s+/g, " ")
  .replace(/\s*([{}:;,])\s*/g, "$1")
  .replace(/;}/g, "}")
  .trim();
fs.writeFileSync("./dist/assets/app.css", minified + "\n");
fs.mkdirSync("./src/public/assets", { recursive: true });
fs.writeFileSync("./src/public/assets/app.css", minified + "\n");
console.log("  ✓ Minified CSS written (" + Buffer.byteLength(minified) + " bytes)");
'

echo "==> [4/4] Validating PHP syntax & running security tests..."
php -l dist/index.php
php -l dist/api.php
php -l dist/views/landing.php
php tests/php/SvgGeneratorTest.php
php tests/php/SecurityTest.php

echo "✅ Build complete! Payload ready in ./dist/"
