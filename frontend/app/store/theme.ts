import { reactive, readonly } from "vue";
import{ themes,type ThemeName} from "~/types/themes";
const state =reactive({
  currentTheme:'light'
});

export const useTheme=()=>{
  const setTheme=(themeName : ThemeName)=>{
    if (!themes[themeName]) return;
    state.currentTheme=themeName;
    const root=document.documentElement;
    Object.entries(themes[themeName]).forEach(([property,value])=>{
      root.style.setProperty(property,value);
    });
  };
  return {
    theme:readonly(state),
    setTheme
  }
}