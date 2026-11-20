/**
 * placeholger.js — Client-Side Zero-Network On-The-Fly SVG Placeholder SDK
 * https://placeholger.hellinger.wtf/placeholger.js
 *
 * Capabilities:
 * 1. Custom Element: <place-holger w="600" h="400" bg="9e9ea0" fg="ffffff" text="PLACEHOLGER" font="sans" dims></place-holger>
 * 2. Data Attribute Hydration: <img data-placeholger="600/400/9e9ea0/ffffff?text=Hero+Banner&font=mono" alt="..." />
 * 3. Programmatic API:
 *    - window.Placeholger.svg({ w, h, bg, fg, text, showDims, font })
 *    - window.Placeholger.dataUri({ w, h, bg, fg, text, showDims, font })
 *    - window.Placeholger.parseSpec("600/400/9e9ea0/ffffff?text=Banner&font=serif")
 */
(function (root) {
  'use strict';

  var DEFAULT_W = 600;
  var DEFAULT_H = 400;
  var DEFAULT_BG = '9E9EA0';
  var DEFAULT_FG = 'FFFFFF';
  var DEFAULT_TEXT = 'PLACEHOLGER';
  var DEFAULT_FONT = 'sans';
  var FONTS = {
    sans: {
      stack: "'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
      widthFactor: 0.66
    },
    mono: {
      stack: "'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace",
      widthFactor: 0.68
    },
    serif: {
      stack: "ui-serif, Georgia, Cambria, 'Times New Roman', Times, serif",
      widthFactor: 0.62
    },
    rounded: {
      stack: "ui-rounded, 'SF Pro Rounded', 'Hiragino Maru Gothic ProN', Quicksand, sans-serif",
      widthFactor: 0.66
    },
    condensed: {
      stack: "'Arial Narrow', 'Roboto Condensed', 'Helvetica Neue Condensed', Impact, sans-serif",
      widthFactor: 0.56
    }
  };

  var HEX_RE = /^#?([0-9a-f]{3}|[0-9a-f]{6})$/i;
  var XML_RE = /[<>&"']/g;
  var XML_ENTITIES = {
    '<': '&lt;',
    '>': '&gt;',
    '&': '&amp;',
    '"': '&quot;',
    "'": '&apos;'
  };

  function r2(n) {
    return Math.round(n * 100) / 100;
  }

  function sanitizeHex(input, fallback) {
    if (!input || typeof input !== 'string') return fallback;
    var m = HEX_RE.exec(input.trim());
    if (!m) return fallback;
    var clean = m[1].toUpperCase();
    return clean.length === 3
      ? clean[0] + clean[0] + clean[1] + clean[1] + clean[2] + clean[2]
      : clean;
  }

  function sanitizeFont(input) {
    if (!input || typeof input !== 'string') return DEFAULT_FONT;
    var clean = input.trim().toLowerCase();
    return Object.prototype.hasOwnProperty.call(FONTS, clean) ? clean : DEFAULT_FONT;
  }

  function escapeXml(str) {
    return String(str).replace(XML_RE, function (c) {
      return XML_ENTITIES[c];
    });
  }

  function clampDim(val, fallback) {
    var n = parseInt(val, 10);
    if (isNaN(n)) return fallback;
    return Math.min(2048, Math.max(1, n));
  }

  /**
   * Parses a shorthand spec string like "600/400/0f172a/38bdf8?text=Hero&font=mono&dims=1"
   * or "600x400/9e9ea0/ffffff" or "512" into options.
   */
  function parseSpec(spec) {
    if (!spec || typeof spec !== 'string') {
      return { w: DEFAULT_W, h: DEFAULT_H, bg: DEFAULT_BG, fg: DEFAULT_FG, text: DEFAULT_TEXT, showDims: false, font: DEFAULT_FONT };
    }
    var parts = spec.replace(/^https?:\/\/[^/]+\//i, '').replace(/^\/+/, '').split('?');
    var pathPart = parts[0] || '';
    var queryPart = parts[1] || '';
    var segments = pathPart ? pathPart.split('/').filter(Boolean) : [];
    var params = new URLSearchParams(queryPart);

    var rawW = params.get('w');
    var rawH = params.get('h');
    var rawBg = params.get('bg');
    var rawFg = params.get('fg');

    if (!rawW && segments.length >= 1) {
      var m = /^(\d+)[xX](\d+)$/.exec(segments[0]);
      if (m) {
        rawW = m[1];
        rawH = m[2];
        rawBg = rawBg || segments[1];
        rawFg = rawFg || segments[2];
      } else {
        rawW = segments[0];
        if (segments.length === 1) {
          rawH = rawW;
        } else if (/^\d+$/.test(segments[1]) && parseInt(segments[1], 10) <= 2048 && segments.length !== 3) {
          rawH = segments[1];
          rawBg = rawBg || segments[2];
          rawFg = rawFg || segments[3];
        } else if (/^\d+$/.test(segments[1]) && segments.length === 2) {
          rawH = segments[1];
        } else {
          rawH = rawW;
          rawBg = rawBg || segments[1];
          rawFg = rawFg || segments[2];
        }
      }
    }

    var w = clampDim(rawW, DEFAULT_W);
    var h = clampDim(rawH, rawW ? w : DEFAULT_H);
    var bg = sanitizeHex(rawBg, DEFAULT_BG);
    var fg = sanitizeHex(rawFg, DEFAULT_FG);
    var rawText = (params.get('text') || '').trim();
    var text = rawText ? rawText.slice(0, 64) : DEFAULT_TEXT;
    var dimsParam = (params.get('dims') || params.get('sub') || '').toLowerCase();
    var showDims = dimsParam === '1' || dimsParam === 'true' || dimsParam === 'yes';
    var font = sanitizeFont(params.get('font'));

    return { w: w, h: h, bg: bg, fg: fg, text: text, showDims: showDims, font: font };
  }

  /**
   * Generates the exact SVG wireframe markup matching the PHP engine & grafik.png.
   */
  function generateSvg(opts) {
    var o = opts || {};
    var w = clampDim(o.w, DEFAULT_W);
    var h = clampDim(o.h, o.w ? w : DEFAULT_H);
    var bg = '#' + sanitizeHex(o.bg, DEFAULT_BG);
    var fg = '#' + sanitizeHex(o.fg, DEFAULT_FG);
    var rawText = typeof o.text === 'string' && o.text.trim() !== '' ? o.text.trim().slice(0, 64) : DEFAULT_TEXT;
    var safeText = escapeXml(rawText);
    var showDims = Boolean(o.showDims || o.dims);
    var fontKey = sanitizeFont(o.font);
    var fontCfg = FONTS[fontKey];

    var minDim = Math.min(w, h);
    var strokeWidth = Math.max(1.5, Math.min(6.0, r2(minDim * 0.0055)));

    var decodedLen = Math.max(1, rawText.length);
    var maxTextWidth = w * 0.80;
    var baseFontSize = minDim * 0.095;
    var widthBoundFontSize = maxTextWidth / Math.max(4, decodedLen * fontCfg.widthFactor);
    var fontSize = Math.max(8, Math.min(96, Math.round(Math.min(baseFontSize, widthBoundFontSize))));

    var gapFraction = showDims ? 0.22 : 0.17;
    var stopRatio = Math.max(0.20, 0.50 - gapFraction);

    var xLeft = r2(w * stopRatio);
    var xRight = r2(w * (1.0 - stopRatio));
    var yTop = r2(h * stopRatio);
    var yBottom = r2(h * (1.0 - stopRatio));

    var textY = r2(showDims ? h * 0.47 : h * 0.50);
    var subFontSize = Math.max(8, Math.round(fontSize * 0.42));
    var subY = r2(textY + fontSize * 0.72);
    var letterSpacing = r2(fontSize * 0.06);

    var subtitleSvg = '';
    if (showDims && minDim >= 48) {
      subtitleSvg = '\n  <text aria-hidden="true" x="50%" y="' + subY + '" fill="' + fg + '" font-family="\'JetBrains Mono\', ui-monospace, monospace" font-size="' + subFontSize + '" font-weight="500" text-anchor="middle" dominant-baseline="central">' + w + ' × ' + h + '</text>';
    }

    return '<svg xmlns="http://www.w3.org/2000/svg" width="' + w + '" height="' + h + '" viewBox="0 0 ' + w + ' ' + h + '" role="img" aria-label="' + safeText + ' (' + w + 'x' + h + ')">\n' +
      '  <title>' + safeText + ' (' + w + 'x' + h + ')</title>\n' +
      '  <rect width="100%" height="100%" fill="' + bg + '"/>\n' +
      '  <g aria-hidden="true" stroke="' + fg + '" stroke-width="' + strokeWidth + '" stroke-linecap="round">\n' +
      '    <line x1="0" y1="0" x2="' + xLeft + '" y2="' + yTop + '"/>\n' +
      '    <line x1="' + w + '" y1="0" x2="' + xRight + '" y2="' + yTop + '"/>\n' +
      '    <line x1="0" y1="' + h + '" x2="' + xLeft + '" y2="' + yBottom + '"/>\n' +
      '    <line x1="' + w + '" y1="' + h + '" x2="' + xRight + '" y2="' + yBottom + '"/>\n' +
      '  </g>\n' +
      '  <text aria-hidden="true" x="50%" y="' + textY + '" fill="' + fg + '" font-family="' + fontCfg.stack + '" font-size="' + fontSize + '" font-weight="700" letter-spacing="' + letterSpacing + 'px" text-anchor="middle" dominant-baseline="central">' + safeText + '</text>' + subtitleSvg + '\n' +
      '</svg>';
  }

  function generateDataUri(opts) {
    var svg = typeof opts === 'string' ? generateSvg(parseSpec(opts)) : generateSvg(opts);
    return 'data:image/svg+xml;utf8,' + encodeURIComponent(svg);
  }

  function hydrateElement(el) {
    var spec = el.getAttribute('data-placeholger');
    if (!spec) return;
    var opts = parseSpec(spec);
    var v;
    if ((v = el.getAttribute('data-w')) !== null) opts.w = clampDim(v, opts.w);
    if ((v = el.getAttribute('data-h')) !== null) opts.h = clampDim(v, opts.h);
    if ((v = el.getAttribute('data-bg')) !== null) opts.bg = sanitizeHex(v, opts.bg);
    if ((v = el.getAttribute('data-fg')) !== null) opts.fg = sanitizeHex(v, opts.fg);
    if ((v = el.getAttribute('data-text')) !== null) opts.text = v;
    if ((v = el.getAttribute('data-font')) !== null) opts.font = sanitizeFont(v);
    if (el.hasAttribute('data-dims')) opts.showDims = true;

    if (el.tagName === 'IMG') {
      el.setAttribute('src', generateDataUri(opts));
      if (!el.hasAttribute('width')) el.setAttribute('width', String(opts.w));
      if (!el.hasAttribute('height')) el.setAttribute('height', String(opts.h));
      if (!el.hasAttribute('alt')) el.setAttribute('alt', opts.text + ' (' + opts.w + 'x' + opts.h + ')');
    } else {
      el.innerHTML = generateSvg(opts);
    }
  }

  function hydrateAll(container) {
    if (typeof document === 'undefined') return;
    var scope = container || document;
    var nodes = scope.querySelectorAll('[data-placeholger]');
    for (var i = 0; i < nodes.length; i++) {
      hydrateElement(nodes[i]);
    }
  }

  // Register <place-holger> Custom Web Component if in browser environment
  if (typeof window !== 'undefined' && 'customElements' in window && !window.customElements.get('place-holger')) {
    class PlaceHolgerElement extends HTMLElement {
      static get observedAttributes() {
        return ['w', 'h', 'width', 'height', 'bg', 'fg', 'text', 'font', 'dims', 'spec'];
      }
      connectedCallback() {
        this.render();
      }
      attributeChangedCallback() {
        if (this.isConnected) this.render();
      }
      render() {
        var specAttr = this.getAttribute('spec');
        var base = specAttr ? parseSpec(specAttr) : {};
        var wAttr = this.getAttribute('w');
        var w = wAttr || this.getAttribute('width') || base.w || DEFAULT_W;
        var h = this.getAttribute('h') || this.getAttribute('height') || base.h || (wAttr ? w : DEFAULT_H);
        var bg = this.getAttribute('bg') || base.bg || DEFAULT_BG;
        var fg = this.getAttribute('fg') || base.fg || DEFAULT_FG;
        var textAttr = this.getAttribute('text');
        var text = textAttr !== null ? textAttr : (base.text || DEFAULT_TEXT);
        var font = sanitizeFont(this.getAttribute('font') || base.font || DEFAULT_FONT);
        var dimsAttr = this.getAttribute('dims');
        var showDims = dimsAttr !== null ? (dimsAttr !== 'false' && dimsAttr !== '0') : Boolean(base.showDims);

        this.style.display = this.style.display || 'inline-block';
        this.style.lineHeight = '0';
        this.innerHTML = generateSvg({ w: w, h: h, bg: bg, fg: fg, text: text, font: font, showDims: showDims });
        var svgEl = this.firstElementChild;
        if (svgEl) {
          svgEl.style.maxWidth = '100%';
          svgEl.style.height = 'auto';
        }
      }
    }
    window.customElements.define('place-holger', PlaceHolgerElement);
  }

  if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function () { hydrateAll(document); });
    } else {
      hydrateAll(document);
    }
  }

  var PlaceholgerAPI = {
    version: '1.1.1',
    svg: generateSvg,
    dataUri: generateDataUri,
    parseSpec: parseSpec,
    hydrate: hydrateAll,
    sanitizeHex: sanitizeHex,
    sanitizeFont: sanitizeFont
  };

  root.Placeholger = PlaceholgerAPI;
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = PlaceholgerAPI;
  }
})(typeof globalThis !== 'undefined' ? globalThis : (typeof window !== 'undefined' ? window : this));
