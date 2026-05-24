// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  modules:[ '@pinia/nuxt',],
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
      googleCallBack: process.env.NUXT_GOOGLE_CALL_BACK ||   'http://localhost:3000/accounts/callback',
      googleClientID : process.env.NUXT_GOOGLE_CLIENT_ID ||'450162713360-9j5n4trhdi4ljm2d5g4cjcidk15bvslo.apps.googleusercontent.com',
    },
  },
})
