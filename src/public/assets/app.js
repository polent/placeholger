/**
 * placeholger Studio Interactive Controller & Agentic Browser (WebMCP) Bridge
 */
(function () {
  'use strict';

  var BASE_URL = 'https://placeholger.hellinger.wtf';
  var currentThemePreference = 'system';
  var currentAspectRatio = 600 / 400;
  var isAspectLocked = false;
  var showDimensions = false;

  function announceStatus(message) {
    var statusEl = document.getElementById('srStatus');
    if (!statusEl) return;
    statusEl.textContent = '';
    setTimeout(function () {
      statusEl.textContent = message;
    }, 20);
  }

  function getSystemTheme() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function applyTheme(effectiveTheme) {
    var root = document.documentElement;
    if (effectiveTheme === 'dark') {
      root.classList.add('dark');
      root.classList.remove('light');
    } else {
      root.classList.add('light');
      root.classList.remove('dark');
    }
  }

  function updateThemeButtons(pref) {
    var btnLight = document.getElementById('themeBtnLight');
    var btnDark = document.getElementById('themeBtnDark');
    var btnSys = document.getElementById('themeBtnSystem');
    if (!btnLight || !btnDark || !btnSys) return;

    var inactiveClass = 'w-7 h-7 rounded text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center';
    var activeClass = 'w-7 h-7 rounded bg-surface-container-highest text-primary font-medium shadow-sm transition-colors flex items-center justify-center';

    btnLight.className = pref === 'light' ? activeClass : inactiveClass;
    btnDark.className = pref === 'dark' ? activeClass : inactiveClass;
    btnSys.className = pref === 'system' ? activeClass : inactiveClass;

    btnLight.setAttribute('aria-pressed', pref === 'light' ? 'true' : 'false');
    btnDark.setAttribute('aria-pressed', pref === 'dark' ? 'true' : 'false');
    btnSys.setAttribute('aria-pressed', pref === 'system' ? 'true' : 'false');
  }

  window.setTheme = function (pref) {
    currentThemePreference = pref;
    try {
      localStorage.setItem('placeholger_theme', pref);
    } catch (e) {}

    if (pref === 'system') {
      applyTheme(getSystemTheme());
    } else {
      applyTheme(pref);
    }
    updateThemeButtons(pref);
  };

  (function initTheme() {
    var saved = 'system';
    try {
      saved = localStorage.getItem('placeholger_theme') || 'system';
    } catch (e) {}
    currentThemePreference = saved;
    applyTheme(saved === 'system' ? getSystemTheme() : saved);
    updateThemeButtons(saved);

    if (window.matchMedia) {
      var mq = window.matchMedia('(prefers-color-scheme: dark)');
      if (mq.addEventListener) {
        mq.addEventListener('change', function (e) {
          if (currentThemePreference === 'system') {
            applyTheme(e.matches ? 'dark' : 'light');
          }
        });
      }
    }
  })();

  function getState() {
    var wInput = document.getElementById('inputWidth');
    var hInput = document.getElementById('inputHeight');
    var bgInput = document.getElementById('inputBg');
    var fgInput = document.getElementById('inputFg');
    var textInput = document.getElementById('inputText');
    var dimsCb = document.getElementById('showDimensionsCheckbox');

    var w = Math.min(2048, Math.max(1, parseInt(wInput ? wInput.value : 600, 10) || 600));
    var h = Math.min(2048, Math.max(1, parseInt(hInput ? hInput.value : 400, 10) || 400));
    var bg = window.Placeholger ? window.Placeholger.sanitizeHex(bgInput ? bgInput.value : '9E9EA0', '9E9EA0') : '9E9EA0';
    var fg = window.Placeholger ? window.Placeholger.sanitizeHex(fgInput ? fgInput.value : 'FFFFFF', 'FFFFFF') : 'FFFFFF';
    var rawText = textInput ? textInput.value.trim() : '';
    var dims = dimsCb ? Boolean(dimsCb.checked) : showDimensions;

    return { w: w, h: h, bg: bg, fg: fg, rawText: rawText, text: rawText || 'PLACEHOLGER', showDims: dims };
  }

  window.handleDimensionChange = function (changedAxis) {
    var wInput = document.getElementById('inputWidth');
    var hInput = document.getElementById('inputHeight');
    var w = parseInt(wInput.value, 10) || 1;
    var h = parseInt(hInput.value, 10) || 1;

    if (w > 2048) { w = 2048; wInput.value = 2048; }
    if (h > 2048) { h = 2048; hInput.value = 2048; }
    if (w < 1) { w = 1; wInput.value = 1; }
    if (h < 1) { h = 1; hInput.value = 1; }

    if (isAspectLocked) {
      if (changedAxis === 'height') {
        w = Math.min(2048, Math.max(1, Math.round(h * currentAspectRatio)));
        wInput.value = w;
      } else {
        h = Math.min(2048, Math.max(1, Math.round(w / currentAspectRatio)));
        hInput.value = h;
      }
    }

    updateUI(w, h);
  };

  window.syncRangeToInput = function (axis) {
    if (axis === 'width') {
      document.getElementById('inputWidth').value = document.getElementById('rangeWidth').value;
    } else {
      document.getElementById('inputHeight').value = document.getElementById('rangeHeight').value;
    }
    window.handleDimensionChange(axis);
  };

  window.applyPreset = function (w, h) {
    document.getElementById('inputWidth').value = w;
    document.getElementById('inputHeight').value = h;
    currentAspectRatio = w / h;
    updateUI(w, h);
  };

  window.handleHexInputChange = function (type, value) {
    var clean = (value || '').replace(/[^0-9a-fA-F]/g, '').slice(0, 6);
    if (type === 'bg') {
      document.getElementById('inputBg').value = clean.toUpperCase();
      if (clean.length === 3 || clean.length === 6) {
        var fullBg = clean.length === 3 ? clean.split('').map(function (c) { return c + c; }).join('') : clean;
        document.getElementById('pickerBg').value = '#' + fullBg;
      }
    } else {
      document.getElementById('inputFg').value = clean.toUpperCase();
      if (clean.length === 3 || clean.length === 6) {
        var fullFg = clean.length === 3 ? clean.split('').map(function (c) { return c + c; }).join('') : clean;
        document.getElementById('pickerFg').value = '#' + fullFg;
      }
    }
    var s = getState();
    updateUI(s.w, s.h);
  };

  window.handleColorPickerChange = function (type, value) {
    var hex = (value || '').replace('#', '').toUpperCase();
    if (type === 'bg') {
      document.getElementById('inputBg').value = hex;
    } else {
      document.getElementById('inputFg').value = hex;
    }
    var s = getState();
    updateUI(s.w, s.h);
  };

  window.applyColorPreset = function (bg, fg) {
    document.getElementById('inputBg').value = bg.toUpperCase();
    document.getElementById('pickerBg').value = '#' + bg;
    document.getElementById('inputFg').value = fg.toUpperCase();
    document.getElementById('pickerFg').value = '#' + fg;
    var s = getState();
    updateUI(s.w, s.h);
  };

  window.handleTextChange = function () {
    var textVal = document.getElementById('inputText').value;
    var clearBtn = document.getElementById('clearTextBtn');
    if (clearBtn) {
      if (textVal.trim() !== '') {
        clearBtn.classList.remove('hidden');
      } else {
        clearBtn.classList.add('hidden');
      }
    }
    var s = getState();
    updateUI(s.w, s.h);
  };

  window.clearCustomText = function () {
    var inputEl = document.getElementById('inputText');
    inputEl.value = '';
    var clearBtn = document.getElementById('clearTextBtn');
    if (clearBtn) clearBtn.classList.add('hidden');
    var s = getState();
    updateUI(s.w, s.h);
    inputEl.focus();
  };

  window.applySampleText = function (sample) {
    document.getElementById('inputText').value = sample;
    var clearBtn = document.getElementById('clearTextBtn');
    if (clearBtn) clearBtn.classList.remove('hidden');
    var s = getState();
    updateUI(s.w, s.h);
  };

  window.toggleShowDimensions = function () {
    showDimensions = document.getElementById('showDimensionsCheckbox').checked;
    var s = getState();
    updateUI(s.w, s.h);
  };

  window.toggleLockAspect = function () {
    var cb = document.getElementById('lockAspectCheckbox');
    isAspectLocked = cb.checked;
    if (isAspectLocked) {
      var s = getState();
      currentAspectRatio = s.w / s.h;
    }
  };

  window.resetDefaults = function () {
    document.getElementById('inputWidth').value = 600;
    document.getElementById('inputHeight').value = 400;
    document.getElementById('inputBg').value = '9E9EA0';
    document.getElementById('pickerBg').value = '#9E9EA0';
    document.getElementById('inputFg').value = 'FFFFFF';
    document.getElementById('pickerFg').value = '#FFFFFF';
    document.getElementById('inputText').value = '';
    var clearBtn = document.getElementById('clearTextBtn');
    if (clearBtn) clearBtn.classList.add('hidden');
    document.getElementById('showDimensionsCheckbox').checked = false;
    showDimensions = false;
    document.getElementById('lockAspectCheckbox').checked = false;
    isAspectLocked = false;
    currentAspectRatio = 1.5;

    updateUI(600, 400);
    announceStatus('Configurator reset to defaults');
  };

  function buildPathQuery(s) {
    var bgLower = s.bg.toLowerCase();
    var fgLower = s.fg.toLowerCase();
    var path = s.w + '/' + s.h + '/' + bgLower + '/' + fgLower;
    var queryParams = [];
    if (s.rawText) {
      queryParams.push('text=' + encodeURIComponent(s.rawText));
    }
    if (s.showDims) {
      queryParams.push('dims=1');
    }
    if (queryParams.length > 0) {
      path += '?' + queryParams.join('&');
    }
    return path;
  }

  function updateUI(w, h) {
    var s = getState();
    s.w = w || s.w;
    s.h = h || s.h;

    document.getElementById('rangeWidth').value = Math.min(s.w, 1920);
    document.getElementById('rangeHeight').value = Math.min(s.h, 1080);
    document.getElementById('sliderWidthVal').textContent = s.w + 'px';
    document.getElementById('sliderHeightVal').textContent = s.h + 'px';

    var pathText = buildPathQuery(s);
    var fullUrl = BASE_URL + '/' + pathText;
    var localHref = '/' + pathText;

    document.getElementById('urlPath').textContent = pathText;
    var extBtn = document.getElementById('externalLinkBtn');
    if (extBtn) extBtn.href = localHref;

    var quickEmbed = document.getElementById('quickEmbedCode');
    if (quickEmbed) {
      quickEmbed.textContent = '<img src="' + fullUrl + '" width="' + s.w + '" height="' + s.h + '" alt="' + s.text + '" />';
    }

    document.getElementById('previewHudDim').textContent = s.w + ' × ' + s.h;
    var ratioVal = (s.w / s.h).toFixed(2);
    document.getElementById('previewRatioBadge').textContent = ratioVal + ':1 (' + s.w + ':' + s.h + ')';

    // Render authentic SVG inside the Stage Preview using Placeholger SDK
    var svgMarkup = window.Placeholger ? window.Placeholger.svg(s) : '';
    var stageHost = document.getElementById('previewSvgHost');
    if (stageHost && svgMarkup) {
      stageHost.innerHTML = svgMarkup;
      var renderedSvg = stageHost.querySelector('svg');
      if (renderedSvg) {
        renderedSvg.setAttribute('width', '100%');
        renderedSvg.setAttribute('height', '100%');
        renderedSvg.style.display = 'block';
      }
    }

    var byteSize = svgMarkup ? new Blob([svgMarkup]).size : 280;
    document.getElementById('previewBytesBadge').textContent = byteSize.toLocaleString() + ' bytes';
    document.getElementById('calculatedKB').textContent = byteSize + ' B';

    // Aspect ratio adjustment for preview frame
    var wrapper = document.getElementById('previewCanvasWrapper');
    if (wrapper) {
      var imgAspect = s.w / s.h;
      if (imgAspect >= 1) {
        wrapper.style.width = '90%';
        wrapper.style.height = Math.max(20, Math.min(90, 90 / imgAspect)) + '%';
      } else {
        wrapper.style.height = '90%';
        wrapper.style.width = Math.max(20, Math.min(90, 90 * imgAspect)) + '%';
      }
    }
  }

  window.copyToClipboard = function (text, btnElement) {
    if (!navigator.clipboard) return;
    navigator.clipboard.writeText(text).then(function () {
      announceStatus('Copied to clipboard');
      if (!btnElement) return;
      var originalHtml = btnElement.innerHTML;
      btnElement.innerHTML = '<span>✓ Copied!</span>';
      setTimeout(function () {
        btnElement.innerHTML = originalHtml;
      }, 1600);
    }).catch(function () {
      announceStatus('Copy to clipboard failed');
    });
  };

  window.copySnippet = function (elementId, btnElement) {
    var el = document.getElementById(elementId);
    if (el) window.copyToClipboard(el.textContent, btnElement);
  };

  window.copyMarkup = function (format, btnElement) {
    var s = getState();
    var path = buildPathQuery(s);
    var url = BASE_URL + '/' + path;
    var snippet = '';

    if (format === 'img') {
      snippet = '<img src="' + url + '" width="' + s.w + '" height="' + s.h + '" alt="' + s.text + '" />';
    } else if (format === 'md') {
      snippet = '![' + s.text + '](' + url + ')';
    } else if (format === 'curl') {
      snippet = 'curl -s "' + url + '" -o placeholder-' + s.w + 'x' + s.h + '.svg';
    } else if (format === 'webcomponent') {
      snippet = '<place-holger w="' + s.w + '" h="' + s.h + '" bg="' + s.bg.toLowerCase() + '" fg="' + s.fg.toLowerCase() + '"' +
        (s.rawText ? ' text="' + s.rawText.replace(/"/g, '&quot;') + '"' : '') +
        (s.showDims ? ' dims' : '') + '></place-holger>';
    }

    window.copyToClipboard(snippet, btnElement);
  };

  /**
   * Direct Client-Side Download of SVG or Rasterized PNG from the Browser Canvas
   */
  window.downloadPlaceholder = function (format) {
    var s = getState();
    var svgString = window.Placeholger.svg(s);
    var fileBase = 'placeholger-' + s.w + 'x' + s.h + '-' + s.bg.toLowerCase();

    if (format === 'svg') {
      var blob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
      var url = URL.createObjectURL(blob);
      triggerDownload(url, fileBase + '.svg');
      announceStatus('SVG download started');
      setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
      return;
    }

    if (format === 'png') {
      var canvas = document.createElement('canvas');
      canvas.width = s.w;
      canvas.height = s.h;
      var ctx = canvas.getContext('2d');
      var img = new Image();
      var svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
      var domUrl = URL.createObjectURL(svgBlob);

      img.onload = function () {
        ctx.drawImage(img, 0, 0, s.w, s.h);
        URL.revokeObjectURL(domUrl);
        var pngUrl = canvas.toDataURL('image/png');
        triggerDownload(pngUrl, fileBase + '.png');
        announceStatus('PNG export started');
      };
      img.src = domUrl;
    }
  };

  function triggerDownload(href, filename) {
    var a = document.createElement('a');
    a.href = href;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  /**
   * Agentic Browsing & WebMCP Tool Registration
   * Allows browser-based AI agents to deterministically generate & configure placeholders.
   */
  function executeAgentTool(args) {
    var opts = args || {};
    if (opts.width || opts.w) document.getElementById('inputWidth').value = opts.width || opts.w;
    if (opts.height || opts.h) document.getElementById('inputHeight').value = opts.height || opts.h;
    if (opts.bg) window.handleHexInputChange('bg', opts.bg);
    if (opts.fg) window.handleHexInputChange('fg', opts.fg);
    if (typeof opts.text === 'string') {
      document.getElementById('inputText').value = opts.text;
    }
    if (typeof opts.dims === 'boolean') {
      document.getElementById('showDimensionsCheckbox').checked = opts.dims;
      showDimensions = opts.dims;
    }
    var s = getState();
    updateUI(s.w, s.h);
    var path = buildPathQuery(s);
    return {
      url: BASE_URL + '/' + path,
      relativeUrl: '/' + path,
      htmlTag: '<img src="' + BASE_URL + '/' + path + '" width="' + s.w + '" height="' + s.h + '" alt="' + s.text + '" />',
      webComponentTag: '<place-holger w="' + s.w + '" h="' + s.h + '" bg="' + s.bg.toLowerCase() + '" fg="' + s.fg.toLowerCase() + '" text="' + s.text + '"></place-holger>',
      svg: window.Placeholger.svg(s)
    };
  }

  window.PlaceholgerStudio = {
    generate: executeAgentTool,
    getState: getState
  };

  if (typeof navigator !== 'undefined' && 'modelContext' in navigator && navigator.modelContext && typeof navigator.modelContext.registerTool === 'function') {
    try {
      navigator.modelContext.registerTool({
        name: 'generate_placeholder',
        description: 'Configures and generates a zero-disk SVG wireframe placeholder URL and inline SVG markup.',
        inputSchema: {
          type: 'object',
          properties: {
            width: { type: 'integer', minimum: 1, maximum: 2048, default: 600 },
            height: { type: 'integer', minimum: 1, maximum: 2048, default: 400 },
            bg: { type: 'string', description: 'Hex background color without #', default: '9E9EA0' },
            fg: { type: 'string', description: 'Hex foreground color without #', default: 'FFFFFF' },
            text: { type: 'string', description: 'Centered text label', default: 'PLACEHOLGER' },
            dims: { type: 'boolean', description: 'Show dimensions subtitle', default: false }
          }
        },
        execute: async function (args) {
          return executeAgentTool(args);
        }
      });
    } catch (e) {}
  }

  // Initialize Studio UI on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { updateUI(600, 400); });
  } else {
    updateUI(600, 400);
  }
})();
