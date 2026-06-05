
export interface ThemeTokens {
  colors: {
    primary: string;
    'on-primary': string;
    secondary: string;
    'on-secondary': string;
    surface: string;
    'on-surface': string;
    'on-surface-muted': string;
    background: string;
    'on-background': string;
    'on-background-muted': string;
    error: string;
    'on-error': string;
    outline: string;
  };
  typography: {
    'font-sans': string;
    'font-serif': string;
    'font-mono': string;
  };
}

export type ThemeName = 'light' | 'dark';

export const DEFAULT_THEME_CONFIG: Record<ThemeName, ThemeTokens> = {
  light: {
    colors: {
      primary: '#3B82F6',
      'on-primary': '#FFFFFF',
      secondary: '#10B981',
      'on-secondary': '#000000',
      surface: '#FFFFFF',
      'on-surface': '#1F2937',
      'on-surface-muted': '#6B7280',
      background: '#F9FAFB',
      'on-background': '#111827',
      'on-background-muted': '#4B5563',
      error: '#EF4444',
      'on-error': '#FFFFFF',
      outline: '#E5E7EB',
    },
    typography: {
      'font-sans': "'Inter', sans-serif",
      'font-serif': "'Merriweather', serif",
      'font-mono': "'Fira Code', monospace",
    },
  },
  dark: {
    colors: {
      primary: '#60A5FA',
      'on-primary': '#0F172A',
      secondary: '#34D399',
      'on-secondary': '#000000',
      surface: '#1E293B',
      'on-surface': '#F9FAFB',
      'on-surface-muted': '#9CA3AF',
      background: '#0F172A',
      'on-background': '#F3F4F6',
      'on-background-muted': '#9CA3AF',
      error: '#F87171',
      'on-error': '#000000',
      outline: '#334155',
    },
    typography: {
      'font-sans': "'Inter', sans-serif",
      'font-serif': "'Merriweather', serif",
      'font-mono': "'Fira Code', monospace",
    },
  },
};



