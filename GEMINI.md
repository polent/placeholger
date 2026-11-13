# GEMINI.md — Antigravity Workspace Rules for `placeholger`

## Project Overview
`placeholger` (`https://placeholger.hellinger.wtf`) is a dead-simple, zero-disk dynamic SVG wireframe placeholder service and interactive developer studio.
It supports both server-side PHP SVG streaming (`/600/400/9e9ea0/ffffff?text=...`) and client-side on-the-fly JS rendering (`placeholger.js`).

## Stitch MCP Reference
- **Stitch Project ID**: `15746711994333053624` (`projects/15746711994333053624` — *"Custom Image Placeholder Service"*)
- **Desktop Screen ID**: `a91722ac436c429ea4adde8c71639337`
- **Mobile Screen ID**: `3dcbfb6a30404768b026f4ef8a216573`
- **Original Wireframe Reference Image ID**: `11616470145875354004` (`grafik.png`)
- **Design System**: *Neutral Spec* (Geist + JetBrains Mono, dark/light/system theme variables, `#9E9EA0` wireframe gray with crisp diagonal corner-to-center crosshairs stopping before the centered text label).

## Core Architectural Invariants (MUST FOLLOW)
1. **Pure SVG & Zero Disk Footprint**:
   - Never write generated placeholder images to disk or use raster libraries (GD/Imagick) on the server.
   - All server-side placeholders are streamed in-memory from `src/public/api.php` with `Content-Type: image/svg+xml; charset=utf-8`.
2. **Search Engine Indexing vs. Non-Indexing**:
   - **Generated Images MUST NEVER be indexed**: Every SVG endpoint response MUST emit `X-Robots-Tag: noindex, noimageindex, noarchive, nosnippet` to prevent spider traps and crawl budget waste.
   - **Landing Page (`/`) MUST be indexed**: The Studio landing page (`src/views/landing.php`) must maintain a `100/100` SEO & Performance profile with semantic HTML5, JSON-LD structured data, OpenGraph tags, `/llms.txt`, and `/openapi.json`.
3. **Source vs. Distribution (`src/` -> `dist/`)**:
   - Always edit source files inside `src/`, `tests/`, or `scripts/`.
   - Never hand-edit files in `dist/`. Run `npm run build` to compile CSS, minify JS, and assemble `dist/`.
4. **Security & Secrets**:
   - Dimensions (`w`, `h`) must be strictly validated as integers between `1` and `2048` (return HTTP `400 Bad Request` when out of bounds).
   - Hex colors (`bg`, `fg`) must be validated as 3 or 6 hex characters (`^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$`).
   - Custom text (`?text=...`) must be XML-escaped via `htmlspecialchars(..., ENT_XML1 | ENT_QUOTES, 'UTF-8')`.
   - Never commit `.env` or `.vscode/mcp.json` to version control.

## Key Commands
- `npm run dev`: Build CSS and start local PHP dev server from `src/public` at `http://127.0.0.1:8080`.
- `npm start`: Build `dist/` and start local PHP server from `dist/` at `http://127.0.0.1:8080`.
- `npm run build`: Compile CSS, copy JS/PHP/SEO/Agentic files, and assemble `dist/`.
- `npm test`: Run the PHP contract/unit test suite (`tests/php/SvgGeneratorTest.php`) and E2E test suite (`tests/e2e/studio.test.mjs`).
- `npm run deploy`: Run tests, build `dist/`, and upload to the remote server over **SFTP** (`scripts/deploy-sftp.sh`) using `.env` (`SFTP_*` variables).
