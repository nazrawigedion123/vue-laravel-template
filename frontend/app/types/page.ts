// 1. Define your valid pages as a constant array
const VALID_PAGES = ['home', 'dashboard', 'settings', 'profile','login','register','blogs','blog'] as const

// 2. Derive a TypeScript type from that array
// This creates the type: 'home' | 'dashboard' | 'settings' | 'profile'
export type PageName = typeof VALID_PAGES[number]