---
name: build-and-deploy
description: >-
  Builds the production dist/ directory, runs local dev server, executes PHP
  and E2E test suites, and deploys placeholger to the remote server over SFTP.
  Use this skill whenever the user asks to run locally, build, test, package,
  or deploy the project.
---

# Local Dev, Build, Test & SFTP Deployment Runbook

## 1. Run Locally
- **Development mode** (serves `src/public` on `http://127.0.0.1:8080`):
  ```bash
  npm run dev
  ```
- **Production preview mode** (builds and serves `dist/` on `http://127.0.0.1:8080`):
  ```bash
  npm start
  ```

## 2. Build `dist/` from `src/`
```bash
npm run build
```

## 3. Run Automated Tests
```bash
npm test
```

## 4. SFTP Deployment (`SFTP_*` variables only)

### Option A: Automated GitHub Actions CI/CD (`.github/workflows/deploy.yml`)
- Pushing to `main` automatically executes `npm run build` and `npm test`, then mirrors `./dist/` via SFTP (`lftp`).
- Required GitHub Repository Secrets:
  - `SFTP_HOST`
  - `SFTP_PORT` (optional, defaults to `22`)
  - `SFTP_USER`
  - `SFTP_PASSWORD`
  - `SFTP_REMOTE_DIR` (optional, defaults to `/`)

### Option B: Local CLI SFTP Deployment (`scripts/deploy-sftp.sh`, requires `lftp`)
1. Copy `.env.example` to `.env` and fill in `SFTP_HOST`, `SFTP_PORT`, `SFTP_USER`, `SFTP_PASSWORD`, and `SFTP_REMOTE_DIR`.
2. Run:
   ```bash
   npm run deploy
   ```
