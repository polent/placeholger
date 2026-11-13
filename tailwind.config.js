/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './src/views/**/*.php',
    './src/public/**/*.js'
  ],
  theme: {
    extend: {
      colors: {
        'background': 'var(--bg-primary)',
        'surface': 'var(--bg-primary)',
        'surface-container-lowest': 'var(--surface-lowest)',
        'surface-container-low': 'var(--surface-low)',
        'surface-container': 'var(--surface-card)',
        'surface-container-high': 'var(--surface-high)',
        'surface-container-highest': 'var(--surface-highest)',
        'primary': 'var(--text-primary)',
        'on-primary': 'var(--on-primary-color)',
        'on-surface': 'var(--text-body)',
        'on-surface-variant': 'var(--text-muted)',
        'secondary': 'var(--secondary-color)',
        'error': '#ffb4ab'
      },
      borderRadius: {
        DEFAULT: '0.25rem',
        lg: '0.375rem',
        xl: '0.5rem',
        full: '9999px'
      },
      spacing: {
        'space-xs': '0.25rem',
        'space-sm': '0.5rem',
        'space-md': '1rem',
        'space-lg': '1.5rem',
        'space-xl': '2.5rem',
        'margin': '2rem',
        'margin-mobile': '1rem',
        'gutter': '1.5rem',
        'gutter-mobile': '1rem'
      },
      fontFamily: {
        'headline-xl': ['Geist', 'system-ui', 'sans-serif'],
        'headline-xl-mobile': ['Geist', 'system-ui', 'sans-serif'],
        'headline-lg': ['Geist', 'system-ui', 'sans-serif'],
        'headline-lg-mobile': ['Geist', 'system-ui', 'sans-serif'],
        'headline-md': ['Geist', 'system-ui', 'sans-serif'],
        'body-lg': ['Geist', 'system-ui', 'sans-serif'],
        'body-md': ['Geist', 'system-ui', 'sans-serif'],
        'body-sm': ['Geist', 'system-ui', 'sans-serif'],
        'code-lg': ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
        'code-md': ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
        'label-md': ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
        'label-sm': ['"JetBrains Mono"', 'ui-monospace', 'monospace']
      },
      fontSize: {
        'headline-xl': ['48px', { lineHeight: '56px', letterSpacing: '-0.03em', fontWeight: '600' }],
        'headline-xl-mobile': ['32px', { lineHeight: '40px', letterSpacing: '-0.02em', fontWeight: '600' }],
        'headline-lg': ['32px', { lineHeight: '40px', letterSpacing: '-0.02em', fontWeight: '600' }],
        'headline-lg-mobile': ['24px', { lineHeight: '32px', letterSpacing: '-0.01em', fontWeight: '600' }],
        'headline-md': ['20px', { lineHeight: '28px', letterSpacing: '-0.01em', fontWeight: '500' }],
        'body-lg': ['16px', { lineHeight: '26px', letterSpacing: '-0.005em', fontWeight: '400' }],
        'body-md': ['14px', { lineHeight: '22px', fontWeight: '400' }],
        'body-sm': ['12px', { lineHeight: '18px', fontWeight: '400' }],
        'code-lg': ['16px', { lineHeight: '24px', letterSpacing: '-0.01em', fontWeight: '500' }],
        'code-md': ['13px', { lineHeight: '20px', fontWeight: '400' }],
        'label-md': ['12px', { lineHeight: '16px', letterSpacing: '0.04em', fontWeight: '500' }],
        'label-sm': ['11px', { lineHeight: '14px', letterSpacing: '0.06em', fontWeight: '500' }]
      }
    }
  },
  plugins: []
};
