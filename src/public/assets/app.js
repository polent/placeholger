/**
 * placeholger Studio Interactive Controller & Agentic Browser (WebMCP) Bridge
 */
(function () {
  'use strict';

  var BASE_URL = 'https://placeholger.hellinger.wtf';
  var currentThemePreference = 'system';
  var currentAspectRatio = 600 / 400;
  var isAspectLocked = false;
  var textEncoder = typeof TextEncoder !== 'undefined' ? new TextEncoder() : null;
  var colorSchemeMq = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
  var els = null;

  function cacheElements() {
    if (els) return els;
    var ids = [
      'srStatus', 'themeSegmentedControl', 'themeBtnLight', 'themeBtnDark', 'themeBtnSystem',
      'configuratorForm', 'inputWidth', 'inputHeight', 'rangeWidth', 'rangeHeight',
      'sliderWidthVal', 'sliderHeightVal', 'pickerBg', 'inputBg', 'pickerFg', 'inputFg',
      'showDimensionsCheckbox', 'inputText', 'clearTextBtn', 'selectFont', 'lockAspectCheckbox',
      'urlPath', 'externalLinkBtn', 'quickEmbedCode', 'previewHudDim',
      'previewRatioBadge', 'previewSvgHost', 'previewBytesBadge', 'calculatedKB',
      'previewCanvasWrapper'
    ];
    els = {};
    for (var i = 0; i < ids.length; i++) {
      els[ids[i]] = document.getElementById(ids[i]);
    }
    return els;
  }

  function announceStatus(message) {
    var statusEl = els ? els.srStatus : document.getElementById('srStatus');
    if (!statusEl) return;
    statusEl.textContent = '';
    setTimeout(function () {
      statusEl.textContent = message;
    }, 20);
  }

  function getSystemTheme() {
    return colorSchemeMq && colorSchemeMq.matches ? 'dark' : 'light';
  }

  function applyTheme(effectiveTheme) {
    var cl = document.documentElement.classList;
    if (effectiveTheme === 'dark') {
      cl.add('dark');
      cl.remove('light');
    } else {
      cl.add('light');
      cl.remove('dark');
    }
  }

  function updateThemeButtons(pref) {
    var e = cacheElements();
    if (!e.themeBtnLight || !e.themeBtnDark || !e.themeBtnSystem) return;

    var inactiveClass = 'w-7 h-7 rounded text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center';
    var activeClass = 'w-7 h-7 rounded bg-surface-container-highest text-primary font-medium shadow-sm transition-colors flex items-center justify-center';

    e.themeBtnLight.className = pref === 'light' ? activeClass : inactiveClass;
    e.themeBtnDark.className = pref === 'dark' ? activeClass : inactiveClass;
    e.themeBtnSystem.className = pref === 'system' ? activeClass : inactiveClass;

    e.themeBtnLight.setAttribute('aria-pressed', pref === 'light' ? 'true' : 'false');
    e.themeBtnDark.setAttribute('aria-pressed', pref === 'dark' ? 'true' : 'false');
    e.themeBtnSystem.setAttribute('aria-pressed', pref === 'system' ? 'true' : 'false');
  }

  function setTheme(pref) {
    currentThemePreference = pref;
    try {
      localStorage.setItem('placeholger_theme', pref);
    } catch (err) {}

    applyTheme(pref === 'system' ? getSystemTheme() : pref);
    updateThemeButtons(pref);
  }

  function initTheme() {
    var saved = 'system';
    try {
      saved = localStorage.getItem('placeholger_theme') || 'system';
    } catch (err) {}
    currentThemePreference = saved;
    applyTheme(saved === 'system' ? getSystemTheme() : saved);
    updateThemeButtons(saved);

    if (colorSchemeMq && colorSchemeMq.addEventListener) {
      colorSchemeMq.addEventListener('change', function (ev) {
        if (currentThemePreference === 'system') {
          applyTheme(ev.matches ? 'dark' : 'light');
        }
      });
    }
  }

  function expandHex3(clean) {
    return clean.length === 3
      ? clean[0] + clean[0] + clean[1] + clean[1] + clean[2] + clean[2]
      : clean;
  }

  function getState() {
    var e = cacheElements();
    var w = Math.min(2048, Math.max(1, parseInt(e.inputWidth ? e.inputWidth.value : 600, 10) || 600));
    var h = Math.min(2048, Math.max(1, parseInt(e.inputHeight ? e.inputHeight.value : 400, 10) || 400));
    var bg = window.Placeholger ? window.Placeholger.sanitizeHex(e.inputBg ? e.inputBg.value : '9E9EA0', '9E9EA0') : '9E9EA0';
    var fg = window.Placeholger ? window.Placeholger.sanitizeHex(e.inputFg ? e.inputFg.value : 'FFFFFF', 'FFFFFF') : 'FFFFFF';
    var rawText = e.inputText ? e.inputText.value.trim() : '';
    var showDims = e.showDimensionsCheckbox ? Boolean(e.showDimensionsCheckbox.checked) : false;
    var fontVal = e.selectFont ? e.selectFont.value : 'sans';
    var font = window.Placeholger && window.Placeholger.sanitizeFont ? window.Placeholger.sanitizeFont(fontVal) : fontVal;

    return { w: w, h: h, bg: bg, fg: fg, rawText: rawText, text: rawText || 'PLACEHOLGER', showDims: showDims, font: font };
  }

  function handleDimensionChange(changedAxis) {
    var e = cacheElements();
    var w = Math.min(2048, Math.max(1, parseInt(e.inputWidth.value, 10) || 1));
    var h = Math.min(2048, Math.max(1, parseInt(e.inputHeight.value, 10) || 1));

    if (isAspectLocked) {
      if (changedAxis === 'height') {
        w = Math.min(2048, Math.max(1, Math.round(h * currentAspectRatio)));
      } else {
        h = Math.min(2048, Math.max(1, Math.round(w / currentAspectRatio)));
      }
    }

    e.inputWidth.value = w;
    e.inputHeight.value = h;
    updateUI();
  }

  function syncRangeToInput(axis) {
    var e = cacheElements();
    if (axis === 'width') {
      e.inputWidth.value = e.rangeWidth.value;
    } else {
      e.inputHeight.value = e.rangeHeight.value;
    }
    handleDimensionChange(axis);
  }

  function applyPreset(w, h) {
    var e = cacheElements();
    e.inputWidth.value = w;
    e.inputHeight.value = h;
    currentAspectRatio = w / h;
    updateUI();
  }

  function syncHexInput(type, value, skipUpdate) {
    var e = cacheElements();
    var clean = (value || '').replace(/[^0-9a-fA-F]/g, '').slice(0, 6).toUpperCase();
    var inputEl = type === 'bg' ? e.inputBg : e.inputFg;
    var pickerEl = type === 'bg' ? e.pickerBg : e.pickerFg;

    if (inputEl) inputEl.value = clean;
    if (pickerEl && (clean.length === 3 || clean.length === 6)) {
      pickerEl.value = '#' + expandHex3(clean);
    }
    if (!skipUpdate) updateUI();
  }

  function handleColorPickerChange(type, value) {
    var e = cacheElements();
    var hex = (value || '').replace('#', '').toUpperCase();
    var inputEl = type === 'bg' ? e.inputBg : e.inputFg;
    if (inputEl) inputEl.value = hex;
    updateUI();
  }

  function applyColorPreset(bg, fg) {
    var e = cacheElements();
    var bgUp = bg.toUpperCase();
    var fgUp = fg.toUpperCase();
    e.inputBg.value = bgUp;
    e.pickerBg.value = '#' + bgUp;
    e.inputFg.value = fgUp;
    e.pickerFg.value = '#' + fgUp;
    updateUI();
  }

  function updateClearBtnVisibility(textVal) {
    var e = cacheElements();
    if (!e.clearTextBtn) return;
    if (textVal.trim() !== '') {
      e.clearTextBtn.classList.remove('hidden');
    } else {
      e.clearTextBtn.classList.add('hidden');
    }
  }

  function handleTextChange() {
    var e = cacheElements();
    updateClearBtnVisibility(e.inputText.value);
    updateUI();
  }

  function clearCustomText() {
    var e = cacheElements();
    e.inputText.value = '';
    updateClearBtnVisibility('');
    updateUI();
    e.inputText.focus();
  }

  function applySampleText(sample) {
    var e = cacheElements();
    e.inputText.value = sample;
    updateClearBtnVisibility(sample);
    updateUI();
  }

  function toggleLockAspect() {
    var e = cacheElements();
    isAspectLocked = e.lockAspectCheckbox.checked;
    if (isAspectLocked) {
      var s = getState();
      currentAspectRatio = s.w / s.h;
    }
  }

  function resetDefaults() {
    var e = cacheElements();
    e.inputWidth.value = 600;
    e.inputHeight.value = 400;
    e.inputBg.value = '9E9EA0';
    e.pickerBg.value = '#9E9EA0';
    e.inputFg.value = 'FFFFFF';
    e.pickerFg.value = '#FFFFFF';
    e.inputText.value = '';
    updateClearBtnVisibility('');
    e.showDimensionsCheckbox.checked = false;
    e.lockAspectCheckbox.checked = false;
    if (e.selectFont) e.selectFont.value = 'sans';
    isAspectLocked = false;
    currentAspectRatio = 1.5;

    updateUI();
    announceStatus('Configurator reset to defaults');
  }

  function buildPathQuery(s) {
    var path = s.w + '/' + s.h + '/' + s.bg.toLowerCase() + '/' + s.fg.toLowerCase();
    var queryParts = [];
    if (s.rawText) queryParts.push('text=' + encodeURIComponent(s.rawText));
    if (s.font && s.font !== 'sans') queryParts.push('font=' + encodeURIComponent(s.font));
    if (s.showDims) queryParts.push('dims=1');
    return queryParts.length > 0 ? path + '?' + queryParts.join('&') : path;
  }

  function byteLength(str) {
    if (!str) return 280;
    return textEncoder ? textEncoder.encode(str).length : str.length;
  }

  function updateUI(precomputedState, skipSvgReplace) {
    var e = cacheElements();
    var s = precomputedState || getState();

    e.rangeWidth.value = Math.min(s.w, 1920);
    e.rangeHeight.value = Math.min(s.h, 1080);
    e.sliderWidthVal.textContent = s.w + 'px';
    e.sliderHeightVal.textContent = s.h + 'px';

    var pathText = buildPathQuery(s);
    var fullUrl = BASE_URL + '/' + pathText;

    e.urlPath.textContent = pathText;
    if (e.externalLinkBtn) e.externalLinkBtn.href = '/' + pathText;
    if (e.quickEmbedCode) {
      e.quickEmbedCode.textContent = '<img src="' + fullUrl + '" width="' + s.w + '" height="' + s.h + '" alt="' + s.text + '" />';
    }

    e.previewHudDim.textContent = s.w + ' × ' + s.h;
    e.previewRatioBadge.textContent = (s.w / s.h).toFixed(2) + ':1 (' + s.w + ':' + s.h + ')';

    var svgMarkup = window.Placeholger ? window.Placeholger.svg(s) : '';
    if (!skipSvgReplace && e.previewSvgHost && svgMarkup) {
      e.previewSvgHost.innerHTML = svgMarkup;
      var renderedSvg = e.previewSvgHost.firstElementChild;
      if (renderedSvg) {
        renderedSvg.setAttribute('width', '100%');
        renderedSvg.setAttribute('height', '100%');
        renderedSvg.style.display = 'block';
      }
    }

    var bytes = byteLength(svgMarkup);
    e.previewBytesBadge.textContent = bytes.toLocaleString() + ' bytes';
    e.calculatedKB.textContent = bytes + ' B';

    if (e.previewCanvasWrapper) {
      var imgAspect = s.w / s.h;
      if (imgAspect >= 1) {
        e.previewCanvasWrapper.style.width = '90%';
        e.previewCanvasWrapper.style.height = Math.max(20, Math.min(90, 90 / imgAspect)) + '%';
      } else {
        e.previewCanvasWrapper.style.height = '90%';
        e.previewCanvasWrapper.style.width = Math.max(20, Math.min(90, 90 * imgAspect)) + '%';
      }
    }

    return svgMarkup;
  }

  function copyToClipboard(text, btnElement) {
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
  }

  function copySnippet(elementId, btnElement) {
    var el = document.getElementById(elementId);
    if (el) copyToClipboard(el.textContent, btnElement);
  }

  function copyMarkup(format, btnElement) {
    var s = getState();
    var url = BASE_URL + '/' + buildPathQuery(s);
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
        (s.font && s.font !== 'sans' ? ' font="' + s.font + '"' : '') +
        (s.showDims ? ' dims' : '') + '></place-holger>';
    }

    copyToClipboard(snippet, btnElement);
  }

  function triggerDownload(href, filename) {
    var a = document.createElement('a');
    a.href = href;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  function downloadPlaceholder(format) {
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
        triggerDownload(canvas.toDataURL('image/png'), fileBase + '.png');
        announceStatus('PNG export started');
      };
      img.src = domUrl;
    }
  }

  function executeAgentTool(args) {
    var e = cacheElements();
    var opts = args || {};
    if (opts.width || opts.w) e.inputWidth.value = opts.width || opts.w;
    if (opts.height || opts.h) e.inputHeight.value = opts.height || opts.h;
    if (opts.bg) syncHexInput('bg', opts.bg, true);
    if (opts.fg) syncHexInput('fg', opts.fg, true);
    if (typeof opts.text === 'string') {
      e.inputText.value = opts.text;
      updateClearBtnVisibility(opts.text);
    }
    if (typeof opts.dims === 'boolean') {
      e.showDimensionsCheckbox.checked = opts.dims;
    }
    if (typeof opts.font === 'string' && e.selectFont) {
      e.selectFont.value = window.Placeholger && window.Placeholger.sanitizeFont
        ? window.Placeholger.sanitizeFont(opts.font)
        : opts.font;
    }

    var s = getState();
    var svgMarkup = updateUI(s, false);
    var path = buildPathQuery(s);
    return {
      url: BASE_URL + '/' + path,
      relativeUrl: '/' + path,
      htmlTag: '<img src="' + BASE_URL + '/' + path + '" width="' + s.w + '" height="' + s.h + '" alt="' + s.text + '" />',
      webComponentTag: '<place-holger w="' + s.w + '" h="' + s.h + '" bg="' + s.bg.toLowerCase() + '" fg="' + s.fg.toLowerCase() + '" text="' + s.text + '"' + (s.font && s.font !== 'sans' ? ' font="' + s.font + '"' : '') + '></place-holger>',
      svg: svgMarkup
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
          additionalProperties: false,
          properties: {
            width: { type: 'integer', description: 'Placeholder width in pixels (1 to 2048)', minimum: 1, maximum: 2048, default: 600 },
            height: { type: 'integer', description: 'Placeholder height in pixels (1 to 2048)', minimum: 1, maximum: 2048, default: 400 },
            bg: { type: 'string', description: 'Hex background color without #', pattern: '^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$', default: '9E9EA0' },
            fg: { type: 'string', description: 'Hex foreground color without #', pattern: '^[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$', default: 'FFFFFF' },
            text: { type: 'string', description: 'Centered text label', maxLength: 64, default: 'PLACEHOLGER' },
            font: { type: 'string', description: 'Font family preset', enum: ['sans', 'mono', 'serif', 'rounded', 'condensed'], default: 'sans' },
            dims: { type: 'boolean', description: 'Show dimensions subtitle', default: false }
          }
        },
        execute: async function (args) {
          return executeAgentTool(args);
        }
      });
    } catch (err) {}
  }

  function bindEvents() {
    var e = cacheElements();

    document.addEventListener('click', function (ev) {
      var btn = ev.target.closest('button');
      if (!btn) return;

      var d = btn.dataset;
      if (d.theme) {
        setTheme(d.theme);
      } else if (d.copyTarget) {
        copySnippet(d.copyTarget, btn);
      } else if (d.action === 'reset') {
        resetDefaults();
      } else if (d.action === 'clear-text') {
        clearCustomText();
      } else if (d.action === 'copy-url') {
        copyToClipboard(BASE_URL + '/' + e.urlPath.textContent, btn);
      } else if (d.sample !== undefined) {
        applySampleText(d.sample);
      } else if (d.colorBg && d.colorFg) {
        applyColorPreset(d.colorBg, d.colorFg);
      } else if (d.presetW && d.presetH) {
        applyPreset(parseInt(d.presetW, 10), parseInt(d.presetH, 10));
      } else if (d.copyMarkup) {
        copyMarkup(d.copyMarkup, btn);
      } else if (d.download) {
        downloadPlaceholder(d.download);
      }
    });

    if (e.configuratorForm) {
      e.configuratorForm.addEventListener('input', function (ev) {
        var t = ev.target;
        switch (t.id) {
          case 'inputWidth': handleDimensionChange('width'); break;
          case 'inputHeight': handleDimensionChange('height'); break;
          case 'rangeWidth': syncRangeToInput('width'); break;
          case 'rangeHeight': syncRangeToInput('height'); break;
          case 'pickerBg': handleColorPickerChange('bg', t.value); break;
          case 'pickerFg': handleColorPickerChange('fg', t.value); break;
          case 'inputBg': syncHexInput('bg', t.value, false); break;
          case 'inputFg': syncHexInput('fg', t.value, false); break;
          case 'inputText': handleTextChange(); break;
        }
      });

      e.configuratorForm.addEventListener('change', function (ev) {
        var id = ev.target.id;
        if (id === 'showDimensionsCheckbox' || id === 'selectFont') {
          updateUI();
        } else if (id === 'lockAspectCheckbox') {
          toggleLockAspect();
        }
      });
    }
  }

  function init() {
    cacheElements();
    initTheme();
    bindEvents();
    var initialState = getState();
    var isDefaultInitial = initialState.w === 600 && initialState.h === 400 &&
      initialState.bg === '9E9EA0' && initialState.fg === 'FFFFFF' &&
      !initialState.rawText && !initialState.showDims && initialState.font === 'sans';
    updateUI(initialState, isDefaultInitial);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
