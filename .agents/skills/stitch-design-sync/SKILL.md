---
name: stitch-design-sync
description: >-
  Synchronizes UI designs, screens, and Neutral Spec design tokens between the
  placeholger Stitch project (15746711994333053624) and the local codebase. Use
  this skill whenever the user asks to inspect, update, regenerate, or sync
  Stitch screens or design tokens for placeholger.
---

# Stitch Design Sync (`placeholger`)

## Stitch Project Identifiers
- **Project Name**: `projects/15746711994333053624`
- **Project ID**: `15746711994333053624`
- **Canonical Desktop Screen**: `projects/15746711994333053624/screens/a91722ac436c429ea4adde8c71639337`
- **Canonical Mobile Screen**: `projects/15746711994333053624/screens/3dcbfb6a30404768b026f4ef8a216573`
- **Reference Wireframe Graphic (`grafik.png`)**: `projects/15746711994333053624/screens/11616470145875354004`

## Workflow
1. **Inspecting Current Screens**:
   - Read `/home/holger/.gemini/antigravity-cli/mcp/stitch/get_project.json` or `list_screens.json` if needed, then call `call_mcp_tool` on server `stitch` with `get_project` (`name: "projects/15746711994333053624"`).
2. **Downloading Screen HTML or Screenshots**:
   - Call `download_file` on server `stitch` using the screen's `htmlCode.name` or `screenshot.name`.
3. **Applying Changes Locally**:
   - Map HTML changes into `src/views/landing.php` and interactive logic into `src/public/assets/app.js`.
   - Never leave runtime `<script src="https://cdn.tailwindcss.com">` in `src/views/landing.php`; compile styles via `npm run build` into `dist/assets/app.css`.
4. **Updating Stitch Screens**:
   - Use `edit_screens` on server `stitch` with `projectId: "15746711994333053624"` and `selectedScreenIds: ["a91722ac436c429ea4adde8c71639337", "3dcbfb6a30404768b026f4ef8a216573"]` to push new UI features back to Stitch.
