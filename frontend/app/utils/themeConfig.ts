
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
      primary: '#6200EE',
      'on-primary': '#FFFFFF',
      secondary: '#03DAC6',
      'on-secondary': '#000000',
      surface: '#FFFFFF',
      'on-surface': '#121212',
      'on-surface-muted': '#666666',
      background: '#F8F9FA',
      'on-background': '#121212',
      'on-background-muted': '#444444',
      error: '#B00020',
      'on-error': '#FFFFFF',
      outline: '#E0E0E0',
    },
    typography: {
      'font-sans': "'Inter', sans-serif",
      'font-serif': "'Merriweather', serif",
      'font-mono': "'Fira Code', monospace",
    },
  },
  dark: {
    colors: {
      primary: '#BB86FC',
      'on-primary': '#000000',
      secondary: '#03DAC6',
      'on-secondary': '#000000',
      surface: '#1E1E1E',
      'on-surface': '#FFFFFF',
      'on-surface-muted': '#AAAAAA',
      background: '#121212',
      'on-background': '#E1E1E1',
      'on-background-muted': '#999999',
      error: '#CF6679',
      'on-error': '#000000',
      outline: '#383838',
    },
    typography: {
      'font-sans': "'Inter', sans-serif",
      'font-serif': "'Merriweather', serif",
      'font-mono': "'Fira Code', monospace",
    },
  },
};

