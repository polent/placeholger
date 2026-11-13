# placeholger (`placeholger.hellinger.wtf`)

Dead-simple, ultra-lightweight **zero-disk dynamic SVG wireframe placeholder service** and **client-side JavaScript SDK**. Generates mathematically crisp vector guides with corner-to-center crosshairs, custom hex colors, and custom label text in `<1ms`.

## Features

- **Zero-Disk PHP SVG Engine (`src/public/api.php`)**: Streams pure SVG wireframe vectors directly from memory (`~500 bytes`) with zero temporary files or GD raster overhead.
- **Client-Side "On-The-Fly" JS SDK (`src/public/placeholger.js`)**: Render placeholders directly in the browser with zero network requests using `<place-holger>` Web Components, `data-placeholger` auto-hydrated `<img>` tags, or `window.Placeholger.svg()`.
- **Interactive Studio & Direct Export (`src/views/landing.php`)**: Built from the Stitch *Neutral Spec* design system with live preview, Light/Dark/System themes, 1-click snippet copying, and instant browser `.SVG` & `.PNG` (HTML5 `<canvas>`) downloads.
- **SEO & Strict Image Non-Indexing**:
  - The canonical landing page (`/`) is indexed with rich JSON-LD (`WebApplication` & `WebAPI`), OpenGraph, and `sitemap.xml`.
  - All generated placeholder images strictly emit `X-Robots-Tag: noindex, noimageindex, noarchive, nosnippet` (plus `robots.txt` rules) so search engines never waste crawl budget indexing infinite placeholder URLs.
- **Agentic Browsing Ready**: Includes `/llms.txt`, `/llms-full.txt`, `/openapi.json` (OpenAPI 3.1.0), and WebMCP (`navigator.modelContext`) integration.
- **Antigravity (AGY) Workspace Setup**: Configured with root `GEMINI.md`, `.agents/skills/`, and `.agents/hooks.json`.

## Running Locally

You only need PHP (`>= 8.1`) and Node.js installed locally:

```bash
# Start local development server (serves directly from src/ on http://127.0.0.1:8080)
npm run dev

# Or build dist/ and serve the compiled production bundle locally on http://127.0.0.1:8080
npm start
```

Once running, open **http://127.0.0.1:8080** in your browser:
- Studio Landing Page: `http://127.0.0.1:8080/`
- Sample SVG Endpoint: `http://127.0.0.1:8080/600/400/0f172a/38bdf8?text=Local+Test&dims=1`
- Client-Side JS SDK: `http://127.0.0.1:8080/placeholger.js`

## Build & Automated Tests

```bash
# Compile minified CSS and assemble ./dist/
npm run build

# Run PHP Unit/Contract + E2E/SEO/JS-SDK test suites (41 assertions)
npm test
```

## Deployment via SFTP

All deployments use **SFTP** (`SFTP_*` variables only).

### Option 1: Automated GitHub Actions CI/CD (`.github/workflows/deploy.yml`)
Every push to `main` runs the full PHP + E2E test suite and automatically mirrors `./dist/` via SFTP (`lftp`).
Configure these **GitHub Repository Secrets** (`Settings -> Secrets and variables -> Actions`):
- `SFTP_HOST` (e.g., `placeholger.hellinger.wtf`)
- `SFTP_PORT` (optional, defaults to `22`)
- `SFTP_USER` (SFTP username)
- `SFTP_PASSWORD` (SFTP password)
- `SFTP_REMOTE_DIR` (optional, defaults to `/`, e.g., `/var/www/placeholger`)

### Option 2: Local CLI SFTP Deployment (`npm run deploy`, requires `lftp`)
1. Copy `.env.example` to `.env` and fill in your `SFTP_*` variables:
   ```ini
   SFTP_HOST=placeholger.hellinger.wtf
   SFTP_PORT=22
   SFTP_USER=your_sftp_username
   SFTP_PASSWORD=your_sftp_password
   SFTP_REMOTE_DIR=/
   ```
2. Run:
   ```bash
   npm run deploy
   ```
