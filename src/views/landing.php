<!DOCTYPE html>
<html class="dark" lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>placeholger.hellinger.wtf — Zero-Disk Dynamic SVG Placeholder Engine &amp; JS SDK</title>
  <meta name="description" content="Dead-simple, ultra-lightweight dynamic SVG wireframe placeholder service and client-side JS SDK. Generates mathematically crisp vector guides with custom hex colors, custom label text, zero disk footprint, and instant edge delivery." />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1" />
  <link rel="canonical" href="https://placeholger.hellinger.wtf/" />

  <!-- Agentic Browsing & Machine-Readable Discovery Links -->
  <link rel="alternate" type="text/markdown" title="LLMs Documentation" href="/llms.txt" />
  <link rel="alternate" type="text/plain" title="Full LLMs Documentation" href="/llms-full.txt" />
  <link rel="service-desc" type="application/vnd.oai.openapi+json" title="OpenAPI 3.1 Specification" href="/openapi.json" />
  <link rel="icon" type="image/svg+xml" href="/64/131316/ffffff?text=PH" />

  <!-- OpenGraph / Social Metadata -->
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://placeholger.hellinger.wtf/" />
  <meta property="og:title" content="placeholger.hellinger.wtf — Minimalist SVG Placeholder Engine" />
  <meta property="og:description" content="Instant zero-disk SVG wireframe placeholders via URL (/600/400/9e9ea0/ffffff?text=...) or client-side Web Components (<place-holger>)." />
  <meta property="og:image" content="https://placeholger.hellinger.wtf/1200/630/131316/ffffff?text=PLACEHOLGER&amp;dims=1" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="placeholger.hellinger.wtf — Minimalist SVG Placeholder Engine" />
  <meta name="twitter:description" content="Instant zero-disk SVG wireframe placeholders via URL or client-side JS SDK." />
  <meta name="twitter:image" content="https://placeholger.hellinger.wtf/1200/630/131316/ffffff?text=PLACEHOLGER&amp;dims=1" />

  <!-- Structured Data (JSON-LD) for Search Engines & AI Agents -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "WebApplication",
        "name": "placeholger",
        "url": "https://placeholger.hellinger.wtf/",
        "applicationCategory": "DeveloperApplication",
        "operatingSystem": "All",
        "description": "Zero-disk dynamic SVG wireframe placeholder service and client-side JavaScript SDK with custom dimensions, hex colors, and label text.",
        "offers": {
          "@type": "Offer",
          "price": "0",
          "priceCurrency": "EUR"
        },
        "featureList": [
          "Dynamic SVG wireframe generation (1px to 2048px)",
          "Custom background and foreground hex colors",
          "Custom XML-sanitized text overlay (?text=...)",
          "Client-side zero-network Web Component (<place-holger>) and JS SDK",
          "Instant browser SVG and PNG canvas export",
          "Strict non-indexing (X-Robots-Tag: noindex, noimageindex) on generated images"
        ]
      },
      {
        "@type": "WebAPI",
        "name": "placeholger SVG Placeholder API",
        "documentation": "https://placeholger.hellinger.wtf/llms.txt",
        "termsOfService": "https://placeholger.hellinger.wtf/",
        "provider": {
          "@type": "Organization",
          "name": "hellinger.wtf",
          "url": "https://hellinger.wtf"
        }
      }
    ]
  }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500;600&amp;display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/app.css" />
</head>
<body class="bg-background text-on-surface font-body-md antialiased selection:bg-surface-container-highest selection:text-primary min-h-screen flex flex-col justify-between">

  <!-- TOP NAVIGATION BAR -->
  <header class="fixed top-0 left-0 right-0 w-full z-50 bg-surface/85 backdrop-blur-md border-b border-surface-container-high/40 shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="h-14 max-w-[1120px] mx-auto px-margin-mobile md:px-margin flex items-center justify-between gap-space-md">
      <div class="flex items-center gap-space-md min-w-0">
        <a class="flex items-center gap-space-sm group" href="#studio">
          <span class="w-2.5 h-2.5 rounded bg-primary shrink-0"></span>
          <span class="font-code-md text-code-md font-medium text-primary tracking-tight group-hover:text-secondary transition-colors truncate">placeholger.hellinger.wtf</span>
        </a>
        <div class="flex items-center gap-space-xs shrink-0">
          <span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">v1.1</span>
          <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm text-on-surface">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>&lt;1ms
          </span>
        </div>
      </div>

      <nav aria-label="Primary Navigation" class="hidden md:flex items-center gap-space-md">
        <a class="font-label-md text-label-md text-primary font-medium transition-colors py-1 px-1.5" href="#studio">Studio</a>
        <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors py-1 px-1.5" href="#sdk">JS SDK</a>
        <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors py-1 px-1.5" href="#api">API Spec</a>
        <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors py-1 px-1.5" href="#seo-caching">SEO &amp; Caching</a>
        <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors py-1 px-1.5" href="/llms.txt" target="_blank" rel="noopener">llms.txt</a>
      </nav>

      <div class="flex items-center gap-space-sm shrink-0">
        <!-- Theme Mode Segmented Control -->
        <div aria-label="Theme mode switcher" class="flex items-center p-0.5 rounded bg-surface-container border border-surface-container-highest/60" id="themeSegmentedControl" role="group">
          <button aria-label="Light mode" class="p-1 rounded text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center" id="themeBtnLight" onclick="setTheme('light')" title="Light theme" type="button">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
          </button>
          <button aria-label="Follow system theme" class="p-1 rounded text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center" id="themeBtnSystem" onclick="setTheme('system')" title="System preference" type="button">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
          </button>
          <button aria-label="Dark mode" class="p-1 rounded text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center" id="themeBtnDark" onclick="setTheme('dark')" title="Dark theme" type="button">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
          </button>
        </div>
        <a class="hidden sm:flex items-center gap-1 px-2.5 py-1 rounded bg-surface-container-high hover:bg-surface-container-highest text-on-surface hover:text-primary font-label-sm text-label-sm transition-colors" href="/openapi.json" target="_blank" rel="noopener">OpenAPI</a>
      </div>
    </div>
  </header>

  <main class="w-full pt-14 bg-background flex-1 pb-20 md:pb-0" id="studio">
    <div class="relative w-full overflow-hidden">
      <div class="max-w-[1120px] mx-auto px-margin-mobile md:px-margin pt-space-lg pb-space-xl flex flex-col gap-space-xl">

        <!-- HERO & PRIMARY VALUE PROP -->
        <section class="flex flex-col gap-space-md pt-space-sm">
          <div class="flex flex-wrap items-center gap-space-xs">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-surface-container-high text-primary font-label-sm text-label-sm tracking-wider uppercase font-medium">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Zero Disk
            </span>
            <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">TTFB &lt;1ms</span>
            <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">Dynamic SVG Vector</span>
            <span class="px-2 py-0.5 rounded bg-surface-container-high text-primary font-label-sm text-label-sm font-medium">Client-Side JS SDK</span>
            <span class="px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">Agentic / WebMCP Ready</span>
          </div>

          <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md">
            <div class="flex flex-col gap-space-xs max-w-2xl">
              <h1 class="font-headline-xl-mobile md:font-headline-xl text-headline-xl-mobile md:text-headline-xl text-primary tracking-tight">
                placeholger<span class="text-on-surface-variant font-code-lg text-code-lg">.hellinger.wtf</span>
              </h1>
              <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
                A dead-simple, ultra-lightweight dynamic SVG wireframe service &amp; client-side JS SDK. Generates mathematically crisp vector guides with corner-to-center crosshairs, custom hex colors, dynamic label text, zero disk footprint, and strict non-indexing headers.
              </p>
            </div>

            <!-- Edge / Indexing Health Badge -->
            <div class="flex items-center gap-space-md p-space-sm rounded bg-surface-container-low border border-surface-container-highest/40 self-start md:self-auto">
              <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Engine Mode</span>
                <span class="font-code-md text-code-md text-primary font-medium flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-emerald-400"></span>PHP + JS On-The-Fly
                </span>
              </div>
              <div class="h-6 w-px bg-surface-container-highest"></div>
              <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Image Robots</span>
                <span class="font-code-md text-code-md text-primary font-medium">noindex, noimageindex</span>
              </div>
            </div>
          </div>

          <!-- Quick 1-Click Embed Snippet -->
          <div class="w-full p-space-sm rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-space-sm">
            <div class="flex items-center gap-space-sm min-w-0 overflow-hidden">
              <span class="font-label-sm text-label-sm text-on-surface-variant px-1.5 py-0.5 rounded bg-surface-container-high shrink-0 font-medium">EMBED</span>
              <code class="font-code-md text-code-md text-on-surface truncate" id="quickEmbedCode">&lt;img src="https://placeholger.hellinger.wtf/600/400/9e9ea0/ffffff" width="600" height="400" alt="PLACEHOLGER" /&gt;</code>
            </div>
            <button data-agent-action="copy-quick-embed" class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded bg-surface-container-high hover:bg-surface-container-highest text-primary font-label-md text-label-md transition-colors shrink-0" id="copyQuickEmbedBtn" onclick="copySnippet('quickEmbedCode', this)" type="button">
              <span>Copy &lt;img&gt;</span>
            </button>
          </div>
        </section>

        <!-- TWO-COLUMN LIVE CONFIGURATOR STAGE -->
        <section aria-label="Interactive Placeholder Configurator" class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">

          <!-- LEFT COLUMN: CONTROLS & QUERY BUILDER (7 Cols) -->
          <div class="lg:col-span-7 flex flex-col gap-space-md p-space-md sm:p-space-lg rounded bg-surface-container-low border border-surface-container-highest/40">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-space-sm">
                <span class="font-code-md text-code-md text-primary font-medium">01 // Configurator</span>
                <span class="text-on-surface-variant font-label-sm text-label-sm">Dynamic Vector Specs</span>
              </div>
              <button data-agent-action="reset-defaults" class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1" onclick="resetDefaults()" type="button">
                <span>↺ Reset</span>
              </button>
            </div>

            <!-- Dimensions Inputs -->
            <div class="flex flex-col gap-space-xs">
              <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Dimensions (Pixels, 1 - 2048)</span>
              <div class="grid grid-cols-2 sm:grid-cols-12 gap-space-sm items-center">
                <div class="col-span-1 sm:col-span-5 flex flex-col gap-1">
                  <label class="font-label-sm text-label-sm text-on-surface-variant" for="inputWidth">Width</label>
                  <div class="flex items-center bg-surface-container-lowest border border-surface-container-highest/50 rounded px-space-sm py-1.5 focus-within:ring-1 focus-within:ring-primary">
                    <input aria-label="Width in pixels" class="w-full bg-transparent font-code-lg text-code-lg text-primary focus:outline-none" id="inputWidth" max="2048" min="1" oninput="handleDimensionChange('width')" type="number" value="600" />
                    <span class="font-label-sm text-label-sm text-on-surface-variant">px</span>
                  </div>
                </div>
                <div class="hidden sm:flex col-span-2 items-center justify-center pt-4">
                  <span class="font-code-lg text-code-lg text-on-surface-variant">×</span>
                </div>
                <div class="col-span-1 sm:col-span-5 flex flex-col gap-1">
                  <label class="font-label-sm text-label-sm text-on-surface-variant" for="inputHeight">Height</label>
                  <div class="flex items-center bg-surface-container-lowest border border-surface-container-highest/50 rounded px-space-sm py-1.5 focus-within:ring-1 focus-within:ring-primary">
                    <input aria-label="Height in pixels" class="w-full bg-transparent font-code-lg text-code-lg text-primary focus:outline-none" id="inputHeight" max="2048" min="1" oninput="handleDimensionChange('height')" type="number" value="400" />
                    <span class="font-label-sm text-label-sm text-on-surface-variant">px</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sliders -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md pt-1">
              <div class="flex flex-col gap-1">
                <div class="flex justify-between font-label-sm text-label-sm text-on-surface-variant">
                  <label for="rangeWidth">Width Slider</label>
                  <span class="font-code-md" id="sliderWidthVal">600px</span>
                </div>
                <input class="w-full accent-primary bg-surface-container-high h-1.5 rounded cursor-pointer" id="rangeWidth" max="1920" min="32" oninput="syncRangeToInput('width')" step="1" type="range" value="600" />
              </div>
              <div class="flex flex-col gap-1">
                <div class="flex justify-between font-label-sm text-label-sm text-on-surface-variant">
                  <label for="rangeHeight">Height Slider</label>
                  <span class="font-code-md" id="sliderHeightVal">400px</span>
                </div>
                <input class="w-full accent-primary bg-surface-container-high h-1.5 rounded cursor-pointer" id="rangeHeight" max="1080" min="32" oninput="syncRangeToInput('height')" step="1" type="range" value="400" />
              </div>
            </div>

            <!-- Color Pickers -->
            <div class="flex flex-col gap-space-xs pt-space-xs">
              <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Color Customization (Hex without #)</span>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                <div class="flex flex-col gap-1">
                  <label class="font-label-sm text-label-sm text-on-surface-variant" for="inputBg">Background (bg)</label>
                  <div class="flex items-center gap-2 bg-surface-container-lowest border border-surface-container-highest/50 rounded px-space-sm py-1.5 focus-within:ring-1 focus-within:ring-primary">
                    <input aria-label="Background color picker" class="w-6 h-6 rounded cursor-pointer border-0 bg-transparent p-0" id="pickerBg" oninput="handleColorPickerChange('bg', this.value)" type="color" value="#9E9EA0" />
                    <span class="font-code-md text-code-md text-on-surface-variant">#</span>
                    <input aria-label="Background hex code" class="w-full bg-transparent font-code-md text-code-md text-primary focus:outline-none uppercase" id="inputBg" maxlength="6" oninput="handleHexInputChange('bg', this.value)" spellcheck="false" type="text" value="9E9EA0" />
                  </div>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="font-label-sm text-label-sm text-on-surface-variant" for="inputFg">Foreground / Crosshairs &amp; Text (fg)</label>
                  <div class="flex items-center gap-2 bg-surface-container-lowest border border-surface-container-highest/50 rounded px-space-sm py-1.5 focus-within:ring-1 focus-within:ring-primary">
                    <input aria-label="Foreground color picker" class="w-6 h-6 rounded cursor-pointer border-0 bg-transparent p-0" id="pickerFg" oninput="handleColorPickerChange('fg', this.value)" type="color" value="#FFFFFF" />
                    <span class="font-code-md text-code-md text-on-surface-variant">#</span>
                    <input aria-label="Foreground hex code" class="w-full bg-transparent font-code-md text-code-md text-primary focus:outline-none uppercase" id="inputFg" maxlength="6" oninput="handleHexInputChange('fg', this.value)" spellcheck="false" type="text" value="FFFFFF" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Custom Text Section -->
            <div class="flex flex-col gap-space-xs pt-space-xs border-t border-surface-container-high/40">
              <div class="flex items-center justify-between flex-wrap gap-2">
                <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="inputText">Custom Text Overlay (?text=...)</label>
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                  <input class="sr-only peer" id="showDimensionsCheckbox" onchange="toggleShowDimensions()" type="checkbox" />
                  <div class="w-8 h-4 bg-surface-container-highest rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-primary after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-600 relative"></div>
                  <span class="font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface">Append Dimensions Subtitle (?dims=1)</span>
                </label>
              </div>
              <div class="flex items-center bg-surface-container-lowest border border-surface-container-highest/50 rounded px-space-sm py-2 focus-within:ring-1 focus-within:ring-primary">
                <span class="font-code-md text-on-surface-variant mr-2">T</span>
                <input aria-label="Custom label text" class="w-full bg-transparent font-code-md text-code-md text-primary placeholder:text-on-surface-variant/50 focus:outline-none" id="inputText" maxlength="64" oninput="handleTextChange()" placeholder="e.g., Hero Banner, Avatar, or leave blank for default 'PLACEHOLGER'" type="text" value="" />
                <button class="text-on-surface-variant hover:text-primary text-xs px-1.5 py-0.5 rounded transition-colors hidden" id="clearTextBtn" onclick="clearCustomText()" type="button">Clear</button>
              </div>
              <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span class="font-label-sm text-label-sm text-on-surface-variant/80">Quick Samples:</span>
                <button class="px-2 py-0.5 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-xs transition-colors" onclick="applySampleText('PLACEHOLGER')" type="button">PLACEHOLGER</button>
                <button class="px-2 py-0.5 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-xs transition-colors" onclick="applySampleText('Hero Banner')" type="button">Hero Banner</button>
                <button class="px-2 py-0.5 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-xs transition-colors" onclick="applySampleText('Product Thumbnail')" type="button">Product Thumbnail</button>
                <button class="px-2 py-0.5 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-xs transition-colors" onclick="applySampleText('Wireframe Box')" type="button">Wireframe Box</button>
                <button class="px-2 py-0.5 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-xs transition-colors" onclick="applySampleText('Avatar / Profile')" type="button">Avatar / Profile</button>
              </div>
            </div>

            <!-- Color Palette Presets -->
            <div class="flex flex-col gap-space-xs pt-space-xs">
              <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Color Palette Presets</span>
              <div class="flex flex-wrap gap-1.5">
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-primary font-code-md text-code-md transition-colors flex items-center gap-1.5" onclick="applyColorPreset('9E9EA0', 'FFFFFF')" type="button">
                  <span class="w-2.5 h-2.5 rounded-full" style="background:#9E9EA0;border:1px solid #FFFFFF;"></span>Default Gray
                </button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors flex items-center gap-1.5" onclick="applyColorPreset('0F172A', '38BDF8')" type="button">
                  <span class="w-2.5 h-2.5 rounded-full" style="background:#0F172A;border:1px solid #38BDF8;"></span>Dark Slate
                </button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors flex items-center gap-1.5" onclick="applyColorPreset('18181B', '22C55E')" type="button">
                  <span class="w-2.5 h-2.5 rounded-full" style="background:#18181B;border:1px solid #22C55E;"></span>Terminal Neon
                </button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors flex items-center gap-1.5" onclick="applyColorPreset('1E3A8A', '93C5FD')" type="button">
                  <span class="w-2.5 h-2.5 rounded-full" style="background:#1E3A8A;border:1px solid #93C5FD;"></span>Blueprint
                </button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors flex items-center gap-1.5" onclick="applyColorPreset('002B36', 'B58900')" type="button">
                  <span class="w-2.5 h-2.5 rounded-full" style="background:#002B36;border:1px solid #B58900;"></span>Solarized
                </button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors flex items-center gap-1.5" onclick="applyColorPreset('F1F5F9', '475569')" type="button">
                  <span class="w-2.5 h-2.5 rounded-full" style="background:#F1F5F9;border:1px solid #475569;"></span>Clean Light
                </button>
              </div>
            </div>

            <!-- Fast Dimension Presets -->
            <div class="flex flex-col gap-space-xs pt-space-xs" id="presets">
              <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Fast Dimension Presets</span>
              <div class="flex flex-wrap gap-1.5">
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors" onclick="applyPreset(320, 640)" type="button">320×640</button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-primary font-code-md text-code-md transition-colors" onclick="applyPreset(600, 400)" type="button">600×400</button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors" onclick="applyPreset(800, 600)" type="button">800×600</button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors" onclick="applyPreset(1024, 768)" type="button">1024×768</button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors" onclick="applyPreset(1200, 630)" type="button">1200×630 (OG)</button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors" onclick="applyPreset(1920, 1080)" type="button">1920×1080</button>
                <button class="px-2.5 py-1 rounded bg-surface-container hover:bg-surface-container-highest text-on-surface hover:text-primary font-code-md text-code-md transition-colors" onclick="applyPreset(512, 512)" type="button">Square 512</button>
              </div>
            </div>

            <!-- Aspect Ratio Lock & Output Size -->
            <div class="flex flex-wrap items-center justify-between gap-space-sm pt-space-xs pb-space-xs">
              <div class="flex items-center gap-space-md">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                  <input class="sr-only peer" id="lockAspectCheckbox" onchange="toggleLockAspect()" type="checkbox" />
                  <div class="w-8 h-4 bg-surface-container-highest rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-primary after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-600 relative"></div>
                  <span class="font-label-sm text-label-sm text-on-surface">Lock Aspect Ratio</span>
                </label>
                <div class="flex items-center gap-1.5 px-2 py-1 rounded bg-surface-container text-on-surface-variant font-label-sm text-label-sm">
                  <span class="text-primary font-medium">Format:</span>
                  <span>SVG (Dynamic Vector)</span>
                </div>
              </div>
              <span class="font-label-sm text-label-sm text-on-surface-variant">Payload: <span class="text-primary font-code-md" id="calculatedKB">~520 B</span></span>
            </div>

            <!-- Live Generated URI -->
            <div class="flex flex-col gap-space-xs pt-space-xs">
              <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Live Generated URI</span>
              <div class="p-space-sm rounded bg-surface-container-lowest border border-surface-container-highest/50 flex items-center justify-between gap-space-sm">
                <div class="font-code-md text-code-md text-primary truncate select-all">
                  https://placeholger.hellinger.wtf/<span class="text-secondary font-medium" id="urlPath">600/400/9e9ea0/ffffff</span>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                  <button data-agent-action="copy-url" class="px-2.5 py-1 rounded bg-surface-container-high hover:bg-surface-container-highest text-primary font-label-sm text-label-sm transition-colors flex items-center gap-1" id="copyUrlBtn" onclick="copyToClipboard('https://placeholger.hellinger.wtf/' + document.getElementById('urlPath').innerText, this)" type="button">
                    <span>Copy URL</span>
                  </button>
                  <a class="px-2 py-1 rounded hover:bg-surface-container-high text-on-surface-variant hover:text-primary transition-colors inline-flex items-center justify-center font-label-sm text-label-sm" href="/600/400/9e9ea0/ffffff" id="externalLinkBtn" rel="noopener" target="_blank" title="Open SVG endpoint directly in new tab">↗</a>
                </div>
              </div>
            </div>

            <!-- Snippet Copy Buttons -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-space-xs pt-space-xs">
              <button class="py-2 px-3 rounded bg-surface-container hover:bg-surface-container-high text-on-surface hover:text-primary font-label-sm text-label-sm text-left flex items-center justify-between transition-colors" onclick="copyMarkup('img', this)" type="button">
                <span>HTML &lt;img&gt;</span>
                <span class="text-on-surface-variant">⎘</span>
              </button>
              <button class="py-2 px-3 rounded bg-surface-container hover:bg-surface-container-high text-on-surface hover:text-primary font-label-sm text-label-sm text-left flex items-center justify-between transition-colors" onclick="copyMarkup('webcomponent', this)" type="button">
                <span>&lt;place-holger&gt;</span>
                <span class="text-on-surface-variant">⎘</span>
              </button>
              <button class="py-2 px-3 rounded bg-surface-container hover:bg-surface-container-high text-on-surface hover:text-primary font-label-sm text-label-sm text-left flex items-center justify-between transition-colors" onclick="copyMarkup('md', this)" type="button">
                <span>Markdown ![]()</span>
                <span class="text-on-surface-variant">⎘</span>
              </button>
              <button class="py-2 px-3 rounded bg-surface-container hover:bg-surface-container-high text-on-surface hover:text-primary font-label-sm text-label-sm text-left flex items-center justify-between transition-colors" onclick="copyMarkup('curl', this)" type="button">
                <span>cURL Snippet</span>
                <span class="text-on-surface-variant">$</span>
              </button>
            </div>
          </div>

          <!-- RIGHT COLUMN: INTERACTIVE CANVAS STAGE / LIVE PREVIEW & EXPORT (5 Cols) -->
          <div class="lg:col-span-5 flex flex-col gap-space-sm">
            <div class="flex items-center justify-between px-space-xs">
              <div class="flex items-center gap-space-xs">
                <span class="font-code-md text-code-md text-primary font-medium">02 // Stage Preview</span>
                <span class="px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">LIVE VECTOR</span>
              </div>
              <div class="flex items-center gap-space-xs font-label-sm text-label-sm text-on-surface-variant">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Zero-Disk Render</span>
              </div>
            </div>

            <!-- Preview Viewport -->
            <div class="relative w-full aspect-square rounded bg-surface-container-lowest p-space-md flex flex-col items-center justify-center overflow-hidden border border-surface-container-highest/40">
              <div class="absolute top-2 left-3 font-code-md text-[10px] text-on-surface-variant select-none tracking-widest">X: 000 | Y: 000</div>
              <div class="absolute top-2 right-3 font-code-md text-[10px] text-on-surface-variant select-none">SVG / VECTOR</div>
              <div class="absolute bottom-2 left-3 font-code-md text-[10px] text-on-surface-variant select-none">STATUS: 200 OK</div>
              <div class="absolute bottom-2 right-3 font-code-md text-[10px] text-on-surface-variant select-none" id="previewHudDim">600 × 400</div>

              <div class="relative max-w-full max-h-full transition-all duration-150 flex items-center justify-center shadow-2xl" id="previewCanvasWrapper" style="width: 90%; height: 60%;">
                <div class="w-full h-full flex items-center justify-center overflow-hidden rounded-sm" id="previewSvgHost">
                  <!-- Populated live by Placeholger.svg() -->
                </div>
              </div>
            </div>

            <!-- Direct Browser Download Actions (SVG & Canvas PNG) -->
            <div class="grid grid-cols-2 gap-space-xs">
              <button data-agent-action="download-svg" class="py-2.5 px-3 rounded bg-primary text-on-primary font-label-md text-label-md font-medium hover:opacity-90 transition-opacity flex items-center justify-center gap-1.5" id="downloadSvgBtn" onclick="downloadPlaceholder('svg')" type="button">
                <span>↓ Download .SVG</span>
              </button>
              <button data-agent-action="download-png" class="py-2.5 px-3 rounded bg-surface-container-high hover:bg-surface-container-highest text-primary font-label-md text-label-md font-medium transition-colors flex items-center justify-center gap-1.5 border border-surface-container-highest" id="downloadPngBtn" onclick="downloadPlaceholder('png')" type="button">
                <span>↓ Export .PNG (Canvas)</span>
              </button>
            </div>

            <!-- Telemetry Metrics Bar -->
            <div class="p-space-sm rounded bg-surface-container-low border border-surface-container-highest/40 grid grid-cols-3 gap-space-sm text-center">
              <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Aspect Ratio</span>
                <span class="font-code-md text-code-md text-primary font-medium" id="previewRatioBadge">1.50:1 (600:400)</span>
              </div>
              <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Payload Weight</span>
                <span class="font-code-md text-code-md text-primary font-medium" id="previewBytesBadge">~520 bytes</span>
              </div>
              <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Edge TTL</span>
                <span class="font-code-md text-code-md text-secondary font-medium">31536000s</span>
              </div>
            </div>
          </div>
        </section>

        <!-- CLIENT-SIDE ON-THE-FLY JS SDK SECTION -->
        <section class="flex flex-col gap-space-md pt-space-md" id="sdk">
          <div class="flex flex-col gap-space-xs">
            <span class="font-code-md text-code-md text-primary font-medium">03 // Client-Side "On-The-Fly" JS SDK</span>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary tracking-tight">Zero-Network Inline SVG Generation in the Browser</h2>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
              Want placeholders generated directly on the client without making individual image requests? Load <code class="font-code-md text-primary">/placeholger.js</code> (~1.8 KB) to unlock the <code class="font-code-md text-primary">&lt;place-holger&gt;</code> Custom Web Component, <code class="font-code-md text-primary">data-placeholger</code> auto-hydration, and programmatic JS APIs.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
            <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-sm">
              <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider">1. Web Component</span>
              <pre class="p-space-sm rounded bg-surface-container-lowest font-code-md text-xs text-on-surface overflow-x-auto"><code>&lt;script src="/js/placeholger.js" defer&gt;&lt;/script&gt;

&lt;place-holger
  w="600" h="400"
  bg="0f172a" fg="38bdf8"
  text="Hero Banner"
  dims&gt;
&lt;/place-holger&gt;</code></pre>
            </div>

            <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-sm">
              <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider">2. Auto-Hydrated &lt;img&gt;</span>
              <pre class="p-space-sm rounded bg-surface-container-lowest font-code-md text-xs text-on-surface overflow-x-auto"><code>&lt;img
  data-placeholger="600/400/18181b/22c55e?text=Instant+Data+URI"
  alt="Client Generated"
/&gt;</code></pre>
            </div>

            <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-sm">
              <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider">3. Live Client Demo</span>
              <div class="flex items-center justify-center bg-surface-container-lowest p-2 rounded border border-surface-container-highest/30">
                <place-holger w="360" h="160" bg="0f172a" fg="38bdf8" text="CLIENT ON-THE-FLY" dims></place-holger>
              </div>
            </div>
          </div>

          <!-- Self-host placeholger.js in your own project -->
          <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-md" id="self-host">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
              <div class="flex flex-col gap-space-xs">
                <span class="font-label-sm text-label-sm text-primary uppercase tracking-wider">Self-host in your own project</span>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                  <code class="font-code-md text-primary">placeholger.js</code> is a single dependency-free file (~1.8 KB) that runs entirely in the browser. Remote hotlinking is blocked (<code class="font-code-md text-primary">403 Forbidden</code>), so save a local copy into your project's static assets:
                </p>
              </div>
              <a class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded bg-primary text-on-primary font-label-md text-label-md font-medium hover:opacity-90 transition-opacity shrink-0" download="placeholger.js" href="/placeholger.js">
                <span>↓ Download placeholger.js</span>
              </a>
            </div>

            <div class="flex flex-col gap-space-sm">
              <pre class="p-space-sm rounded bg-surface-container-lowest font-code-md text-xs text-on-surface overflow-x-auto"><code># 1. Save a local copy in your project
curl -fsSL -o public/js/placeholger.js https://placeholger.hellinger.wtf/placeholger.js

# 2. Load it from your own origin
&lt;script src="/js/placeholger.js" defer&gt;&lt;/script&gt;</code></pre>
              <p class="font-body-sm text-body-sm text-on-surface-variant">No build step or server-side code needed. Once saved locally, the <code class="font-code-md text-primary">&lt;place-holger&gt;</code> component works offline and without external requests.</p>
            </div>

            <div class="flex flex-col gap-space-sm">
              <span class="font-label-sm text-label-sm text-on-surface uppercase tracking-wider">Component attributes</span>
              <div class="flex flex-col divide-y divide-surface-container-high/50 font-body-sm text-body-sm">
                <div class="py-2 flex items-start justify-between gap-space-sm">
                  <span class="font-code-md text-code-md text-primary">w / h</span>
                  <span class="text-on-surface text-right">Width and height in px (1–2048). If only <code class="font-code-md">w</code> is set, it renders a square.</span>
                </div>
                <div class="py-2 flex items-start justify-between gap-space-sm">
                  <span class="font-code-md text-code-md text-primary">bg / fg</span>
                  <span class="text-on-surface text-right">Background and line/text color as 3- or 6-char hex, without <code class="font-code-md">#</code>.</span>
                </div>
                <div class="py-2 flex items-start justify-between gap-space-sm">
                  <span class="font-code-md text-code-md text-primary">text</span>
                  <span class="text-on-surface text-right">Center label (max 64 chars). Defaults to <code class="font-code-md">PLACEHOLGER</code>.</span>
                </div>
                <div class="py-2 flex items-start justify-between gap-space-sm">
                  <span class="font-code-md text-code-md text-primary">dims</span>
                  <span class="text-on-surface text-right">Boolean attribute that adds a <code class="font-code-md">w × h</code> subtitle.</span>
                </div>
                <div class="py-2 flex items-start justify-between gap-space-sm">
                  <span class="font-code-md text-code-md text-primary">spec</span>
                  <span class="text-on-surface text-right">Shorthand alternative, e.g. <code class="font-code-md">spec="600/400/0f172a/38bdf8?text=Hero"</code>.</span>
                </div>
              </div>
              <p class="font-body-sm text-body-sm text-on-surface-variant">
                For scripting there is also <code class="font-code-md text-primary">window.Placeholger.svg({...})</code> (returns SVG markup) and <code class="font-code-md text-primary">window.Placeholger.dataUri({...})</code> (returns a <code class="font-code-md">data:</code> URI for <code class="font-code-md">&lt;img src&gt;</code> or CSS).
              </p>
            </div>
          </div>
        </section>

        <!-- TWO-COLUMN TECHNICAL DOCUMENTATION & ARCHITECTURE SECTION -->
        <section class="flex flex-col gap-space-lg pt-space-md" id="docs">
          <div class="flex flex-col gap-space-xs">
            <span class="font-code-md text-code-md text-primary font-medium">04 // API Specification &amp; Implementation</span>
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary tracking-tight">Zero-Disk Dynamic SVG Pipeline &amp; Edge Delivery</h2>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
              Designed for zero disk wear, instant rendering, and continuous integration workloads. Zero files saved on disk, zero GD raster computation, and strict SEO protection.
            </p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start" id="api">
            <!-- LEFT COLUMN: API SPECIFICATION (6 Cols) -->
            <div class="lg:col-span-6 flex flex-col gap-space-md">
              <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-sm">
                <div class="flex items-center justify-between">
                  <span class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container-high text-primary font-medium">ROUTE 01</span>
                  <span class="font-label-sm text-label-sm text-on-surface-variant font-code-md">GET /{w}/{h}/{bg}/{fg}?text=...</span>
                </div>
                <div class="flex flex-col gap-1">
                  <h3 class="font-headline-md text-headline-md text-primary">Dimension &amp; Color Path + Custom Text</h3>
                  <p class="font-body-md text-body-md text-on-surface-variant">
                    Generates an exact vector SVG wireframe with custom hex background and foreground crosshairs/text. Supports optional <code class="font-code-md text-primary">?text=...</code> and <code class="font-code-md text-primary">?dims=1</code> query parameters.
                  </p>
                </div>
                <div class="p-space-sm rounded bg-surface-container-lowest font-code-md text-code-md text-on-surface flex items-center justify-between overflow-x-auto">
                  <code>https://placeholger.hellinger.wtf/<span class="text-primary font-medium">600</span>/<span class="text-primary font-medium">400</span>/<span class="text-secondary font-medium">0f172a</span>/<span class="text-primary font-medium">38bdf8</span>?<span class="text-primary font-medium">text=Hero+Banner</span></code>
                  <span class="font-label-sm text-label-sm text-on-surface-variant shrink-0 ml-2">200 OK</span>
                </div>
              </div>

              <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-sm">
                <div class="flex items-center justify-between">
                  <span class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container-high text-primary font-medium">ROUTE 02</span>
                  <span class="font-label-sm text-label-sm text-on-surface-variant font-code-md">GET /{size}?text=... (Square or Query Params)</span>
                </div>
                <div class="flex flex-col gap-1">
                  <h3 class="font-headline-md text-headline-md text-primary">Equilateral Square or Query Params</h3>
                  <p class="font-body-md text-body-md text-on-surface-variant">
                    Passed a single dimension, width and height match 1:1. Also supports optional query params: <code class="font-code-md text-primary">?bg=1e3a8a&amp;fg=93c5fd&amp;text=Avatar</code>.
                  </p>
                </div>
                <div class="p-space-sm rounded bg-surface-container-lowest font-code-md text-code-md text-on-surface flex items-center justify-between overflow-x-auto">
                  <code>https://placeholger.hellinger.wtf/<span class="text-primary font-medium">400</span>?bg=1e3a8a&amp;fg=93c5fd&amp;text=Avatar</code>
                  <span class="font-label-sm text-label-sm text-on-surface-variant shrink-0 ml-2">400×400 px</span>
                </div>
              </div>

              <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-sm" id="seo-caching">
                <h3 class="font-headline-md text-headline-md text-primary">HTTP Contract, Caching &amp; Non-Indexing Headers</h3>
                <div class="flex flex-col divide-y divide-surface-container-high/50 font-body-sm text-body-sm">
                  <div class="py-2 flex items-start justify-between gap-space-sm">
                    <span class="font-code-md text-code-md text-primary">Width / Height Bound</span>
                    <span class="text-on-surface text-right font-code-md">Integer between 1 and 2048</span>
                  </div>
                  <div class="py-2 flex items-start justify-between gap-space-sm">
                    <span class="font-code-md text-code-md text-primary">Color Values (bg / fg)</span>
                    <span class="text-on-surface text-right font-code-md">3 or 6 Hex chars ([0-9a-fA-F])</span>
                  </div>
                  <div class="py-2 flex items-start justify-between gap-space-sm">
                    <span class="font-code-md text-code-md text-primary">Custom Text (?text=)</span>
                    <span class="text-on-surface text-right font-code-md">URL-encoded string (XML-sanitized)</span>
                  </div>
                  <div class="py-2 flex items-start justify-between gap-space-sm">
                    <span class="font-code-md text-code-md text-primary">Out of Range (&gt; 2048 or &lt;= 0)</span>
                    <span class="text-error text-right font-code-md">400 Bad Request</span>
                  </div>
                  <div class="py-2 flex items-start justify-between gap-space-sm">
                    <span class="font-code-md text-code-md text-primary">Cache-Control</span>
                    <span class="text-on-surface text-right font-code-md">public, max-age=31536000, immutable</span>
                  </div>
                  <div class="py-2 flex items-start justify-between gap-space-sm">
                    <span class="font-code-md text-code-md text-primary">X-Robots-Tag (Images)</span>
                    <span class="text-emerald-400 text-right font-code-md">noindex, noimageindex, noarchive</span>
                  </div>
                  <div class="py-2 flex items-start justify-between gap-space-sm">
                    <span class="font-code-md text-code-md text-primary">Content-Type</span>
                    <span class="text-on-surface text-right font-code-md">image/svg+xml; charset=utf-8</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- RIGHT COLUMN: CACHE ARCHITECTURE & CODE VIEWER (6 Cols) -->
            <div class="lg:col-span-6 flex flex-col gap-space-md" id="architecture">
              <div class="p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 flex flex-col gap-space-sm">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Execution &amp; SEO Strategy</span>
                <h3 class="font-headline-md text-headline-md text-primary">Zero-Disk Hot Path &amp; Crawler Protection</h3>
                <div class="grid grid-cols-1 gap-2 pt-1">
                  <div class="p-2.5 rounded bg-surface-container flex items-start gap-space-sm">
                    <span class="font-code-md text-code-md px-1.5 py-0.5 rounded bg-surface-container-highest text-primary shrink-0">01</span>
                    <div class="flex flex-col">
                      <span class="font-body-md text-body-md text-primary font-medium">Request &amp; XML Sanitization</span>
                      <span class="font-body-sm text-body-sm text-on-surface-variant">Parses dimensions (1–2048), normalizes 3/6-char hex codes, and escapes <code class="font-code-md text-primary">?text=</code> entities.</span>
                    </div>
                  </div>
                  <div class="p-2.5 rounded bg-surface-container flex items-start gap-space-sm">
                    <span class="font-code-md text-code-md px-1.5 py-0.5 rounded bg-surface-container-highest text-primary shrink-0">02</span>
                    <div class="flex flex-col">
                      <span class="font-body-md text-body-md text-primary font-medium">Deterministic ETag &amp; Immutable Edge Cache</span>
                      <span class="font-body-sm text-body-sm text-on-surface-variant">Emits <code class="font-code-md text-primary">Cache-Control: public, max-age=31536000, immutable</code> and handles <code class="font-code-md text-primary">If-None-Match</code> with instant <code class="font-code-md text-primary">304 Not Modified</code>.</span>
                    </div>
                  </div>
                  <div class="p-2.5 rounded bg-surface-container flex items-start gap-space-sm">
                    <span class="font-code-md text-code-md px-1.5 py-0.5 rounded bg-surface-container-highest text-primary shrink-0">03</span>
                    <div class="flex flex-col">
                      <span class="font-body-md text-body-md text-primary font-medium">Strict Image Non-Indexing (SEO Protection)</span>
                      <span class="font-body-sm text-body-sm text-on-surface-variant">Only this Studio landing page is indexed. All generated SVG images send <code class="font-code-md text-primary">X-Robots-Tag: noindex, noimageindex</code> so search engines never waste crawl budget on placeholder URLs.</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- BENCHMARKS & PROTOCOL METRICS SECTION -->
        <section aria-label="Service Benchmarks" class="grid grid-cols-1 md:grid-cols-4 gap-space-sm p-space-md rounded bg-surface-container-low border border-surface-container-highest/40 items-center">
          <div class="flex flex-col gap-1 border-b md:border-b-0 md:border-r border-surface-container-highest/60 pb-3 md:pb-0 md:pr-space-md">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Storage Footprint</span>
            <span class="font-headline-md text-headline-md text-primary font-bold">0 Files / 0 B</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant">In-Memory String Templating</span>
          </div>
          <div class="flex flex-col gap-1 border-b md:border-b-0 md:border-r border-surface-container-highest/60 pb-3 md:pb-0 md:pr-space-md">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Cold Invocation</span>
            <span class="font-headline-md text-headline-md text-primary font-bold">&lt;1ms</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Zero GD rasterization</span>
          </div>
          <div class="flex flex-col gap-1 border-b md:border-b-0 md:border-r border-surface-container-highest/60 pb-3 md:pb-0 md:pr-space-md">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Search Indexing</span>
            <span class="font-headline-md text-headline-md text-primary font-bold">Landing Only</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Images send X-Robots-Tag: noindex</span>
          </div>
          <div class="flex flex-col gap-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Agentic Readiness</span>
            <span class="font-headline-md text-headline-md text-primary font-bold">llms.txt + OAS</span>
            <span class="font-body-sm text-body-sm text-on-surface-variant">WebMCP &amp; OpenAPI 3.1 enabled</span>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="w-full bg-surface-container-lowest border-t border-surface-container-high/40 pb-16 md:pb-0">
    <div class="max-w-[1120px] mx-auto px-margin-mobile md:px-margin py-space-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-space-lg">
      <div class="flex flex-col gap-space-xs">
        <div class="flex items-center gap-space-sm">
          <span class="font-code-md text-code-md text-primary font-medium">placeholger</span>
          <span class="font-label-sm text-label-sm text-on-surface-variant">— zero overhead placeholder edge API</span>
        </div>
        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-sm">
          Engineered for deterministic layout simulations, responsive prototypes, and AI agent workflows.
        </p>
      </div>
      <div class="flex flex-wrap items-center gap-space-lg font-label-sm text-label-sm text-on-surface-variant">
        <a class="hover:text-primary transition-colors" href="#studio">Configurator</a>
        <a class="hover:text-primary transition-colors" href="#sdk">Client JS SDK</a>
        <a class="hover:text-primary transition-colors" href="#api">API Spec</a>
        <a class="hover:text-primary transition-colors" href="/llms.txt">llms.txt</a>
        <a class="hover:text-primary transition-colors" href="/openapi.json">openapi.json</a>
        <a class="hover:text-primary transition-colors" href="/robots.txt">robots.txt</a>
      </div>
      <div class="font-label-sm text-label-sm text-on-surface-variant">© <?= date('Y') ?> placeholger.hellinger.wtf</div>
    </div>
  </footer>

  <!-- MOBILE BOTTOM NAVIGATION BAR (Matches Stitch Mobile Screen) -->
  <nav aria-label="Mobile Navigation" class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-surface/95 backdrop-blur-xl border-t border-surface-container-high/50">
    <div class="flex justify-around items-center h-14 px-space-xs">
      <a class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 rounded text-primary font-label-sm text-label-sm" href="#studio">
        <span>Studio</span>
      </a>
      <a class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 rounded text-on-surface-variant hover:text-primary font-label-sm text-label-sm" href="#presets">
        <span>Presets</span>
      </a>
      <a class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 rounded text-on-surface-variant hover:text-primary font-label-sm text-label-sm" href="#sdk">
        <span>JS SDK</span>
      </a>
      <a class="flex flex-col items-center justify-center gap-0.5 px-3 py-1 rounded text-on-surface-variant hover:text-primary font-label-sm text-label-sm" href="#api">
        <span>API</span>
      </a>
    </div>
  </nav>

  <script src="/placeholger.js"></script>
  <script src="/assets/app.js"></script>
</body>
</html>
