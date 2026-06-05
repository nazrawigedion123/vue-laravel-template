import { defineStore } from "pinia";
import { DEFAULT_THEME_CONFIG, type ThemeName, type ThemeTokens } from "~/utils/themeConfig";

export const useThemeStore = defineStore("theme", () => {
  const currentTheme = useCookie<ThemeName>("theme", {
    default: () => "light",
    path: "/",
  });

  // State to hold the current theme configuration (can be updated from API)
  const themeConfig = ref<Record<ThemeName, ThemeTokens>>(DEFAULT_THEME_CONFIG);

  // Computed helper for easy access to the active tokens
  const activeThemeTokens = computed(() => themeConfig.value[currentTheme.value]);

  const setTheme = (themeName: ThemeName) => {
    currentTheme.value = themeName;
    applyThemeVariables();
  };

  const toggleTheme = () => {
    const nextTheme = currentTheme.value === "light" ? "dark" : "light";
    setTheme(nextTheme);
  };

  /**
   * Dynamically loads theme data from a remote source.
   * This overrides the local constants and applies the new theme immediately.
   */
  const loadRemoteTheme = async () => {
    try {
      // Future implementation:
      // const response = await $fetch('/api/theme-config');
      // if (response.data) themeConfig.value = response.data;
      
      console.info("Simulating remote theme fetch...");
      await new Promise(resolve => setTimeout(resolve, 800));
      
      // After fetching, re-apply variables
      applyThemeVariables();
    } catch (error) {
      console.error("Failed to load remote theme:", error);
    }
  };

  /**
   * Injects semantic tokens into the document root as CSS custom properties.
   */
  const applyThemeVariables = () => {
    if (process.server) return;
    
    const root = document.documentElement;
    const tokens = activeThemeTokens.value;

    // Map color tokens (e.g. primary -> --color-primary)
    Object.entries(tokens.colors).forEach(([key, value]) => {
      root.style.setProperty(`--color-${key}`, value);
    });

    // Semantic Aliases for existing app styles to ensure "every page" is affected
    root.style.setProperty('--bg-primary', tokens.colors.background);
    root.style.setProperty('--bg-surface', tokens.colors.surface);
    root.style.setProperty('--text-primary', tokens.colors['on-background']);
    root.style.setProperty('--text-muted', tokens.colors['on-background-muted']);
    root.style.setProperty('--border-color', tokens.colors.outline);
    root.style.setProperty('--card-bg', tokens.colors.surface);
    root.style.setProperty('--navbar-bg', tokens.colors.surface);

    // Map typography tokens (e.g. font-sans -> --font-sans)
    Object.entries(tokens.typography).forEach(([key, value]) => {
      root.style.setProperty(`--${key}`, value);
    });
    
    // Global Body overrides
    root.style.setProperty('--font-main', tokens.typography['font-sans']);
  };


  return {
    currentTheme,
    themeConfig,
    activeThemeTokens,
    setTheme,
    toggleTheme,
    loadRemoteTheme,
    applyThemeVariables,
  };
});