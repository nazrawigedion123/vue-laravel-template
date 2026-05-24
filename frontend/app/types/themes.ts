

type ThemeVariables={
    '--bg-primary':string;
    '--bg-surface':string;
    '--text-primary':string;
    '--color-primary':string;

}
export type ThemeName= 'light' | 'dark';

// 3. Apply the types using 'satisfies'
export const themes = {
  light: {
    '--bg-primary': '#ffffff',
    '--bg-surface': '#f8f9fa',
    '--text-primary': '#212529',
    '--color-primary': '#3eaf7c'
  },
  dark: {
    '--bg-primary': '#1a1a1a',
    '--bg-surface': '#2d2d2d',
    '--text-primary': '#f8f9fa',
    '--color-primary': '#42b883'
  }
} satisfies Record<ThemeName, ThemeVariables>;