import type { Config } from 'tailwindcss'

export default <Partial<Config>>{
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: 'var(--color-primary)',
          foreground: 'var(--color-on-primary)',
        },
        secondary: {
          DEFAULT: 'var(--color-secondary)',
          foreground: 'var(--color-on-secondary)',
        },
        surface: {
          DEFAULT: 'var(--bg-surface)',
          foreground: 'var(--color-on-surface)',
          variant: 'var(--color-on-surface-muted)',
        },
        background: {
          DEFAULT: 'var(--bg-primary)',
          foreground: 'var(--text-primary)',
        },
        outline: 'var(--color-outline)',
        error: {
          DEFAULT: 'var(--color-error)',
          foreground: 'var(--color-on-error)',
          container: 'var(--color-error-container, #F9DEDC)', // Fallback to MD3 Light Error Container
          'on-container': 'var(--color-on-error-container, #410E0B)', // Fallback to MD3 Light On Error Container
        },
      },
      borderRadius: {
        '3xl': '24px',
        '4xl': '32px',
      },
      boxShadow: {
        'md': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
      }
    },
  },
}
