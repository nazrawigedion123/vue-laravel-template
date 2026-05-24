

type ThemeVariables = {
  '--bg-primary': string;
  '--bg-surface': string;
  '--text-primary': string;
  '--text-muted': string;
  '--color-primary': string;
  '--border-color': string;
  '--navbar-bg': string;
  '--card-bg': string;
}

export type ThemeName = 'light' | 'dark';

export const themes = {
  light: {
    '--bg-primary': '#ffffff',
    '--bg-surface': '#f8f9fa',
    '--text-primary': '#1e293b',
    '--text-muted': '#64748b',
    '--color-primary': '#3b82f6',
    '--border-color': '#e2e8f0',
    '--navbar-bg': '#ffffff',
    '--card-bg': '#ffffff'
  },
  dark: {
    '--bg-primary': '#0f172a',
    '--bg-surface': '#1e293b',
    '--text-primary': '#f8fafc',
    '--text-muted': '#94a3b8',
    '--color-primary': '#3b82f6',
    '--border-color': '#334155',
    '--navbar-bg': '#0f172a',
    '--card-bg': '#1e293b'
  }
} satisfies Record<ThemeName, ThemeVariables>;