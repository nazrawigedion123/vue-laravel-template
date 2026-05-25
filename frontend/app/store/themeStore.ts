import { defineStore } from "pinia";
import { themes, type ThemeName } from "~/types/themes";

export const useThemeStore = defineStore("theme", () => {
  const currentTheme = useCookie<ThemeName>("theme", {
    default: () => "light",
    path: "/",
  });

  const setTheme = (themeName: ThemeName) => {
    if (!themes[themeName]) return;
    currentTheme.value = themeName;
    applyThemeVariables(themeName);
  };

  const toggleTheme = () => {
    const nextTheme = currentTheme.value === "light" ? "dark" : "light";
    setTheme(nextTheme);
  };

  const applyThemeVariables = (themeName: ThemeName) => {
    // Only run on client side
    if (process.server) return;
    
    const root = document.documentElement;
    Object.entries(themes[themeName]).forEach(([property, value]) => {
      root.style.setProperty(property, value);
    });
  };

  return {
    currentTheme,
    setTheme,
    toggleTheme,
    applyThemeVariables,
  };
});