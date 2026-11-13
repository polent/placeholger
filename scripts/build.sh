#!/usr/bin/env bash
# Builds the production dist/ directory from src/ (Zero external network dependencies required)
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

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

echo "==> [4/4] Validating PHP syntax in dist/..."
php -l dist/index.php
php -l dist/api.php
php -l dist/views/landing.php

echo "✅ Build complete! Payload ready in ./dist/"
