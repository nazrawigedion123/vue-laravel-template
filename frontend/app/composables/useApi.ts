// // frontend/app/composables/useApi.ts
// import { type RequestOptions } from "~/types/api";
// export const useApi = () => {
//   const config = useRuntimeConfig()
//   const baseUrl = String(config.public.apiBase).replace(/\/$/, '')

//   const request = async <T>(path: string, options: RequestOptions = {}) => {
//     const headers: HeadersInit = {
//       Accept: 'application/json',
//     }

//     if (options.body) {
//       headers['Content-Type'] = 'application/json'
//     }

//     if (options.token) {
//       headers.Authorization = `Bearer ${options.token}`
//     }

//     return await $fetch<T>(`${baseUrl}${path}`, {
//       method: options.method || 'GET',
//       body: options.body,
//       query: options.query,
//       headers,
//     })
//   }

//   return { request }
// }


// frontend/app/composables/useApi.ts
import { type RequestOptions } from "~/types/api"

export const useApi = () => {
  const config = useRuntimeConfig()
  const baseUrl = String(config.public.apiBase).replace(/\/$/, '')

  const request = async <T>(path: string, options: RequestOptions = {}): Promise<T> => {
    // Leverage native $fetch configuration merging
    const headers: Record<string, string> = {
      Accept: 'application/json',
      ...((options.body && { 'Content-Type': 'application/json' }) || {}),
      ...((options.token && { Authorization: `Bearer ${options.token}` }) || {}),
    }

    return await $fetch<T>(`${baseUrl}${path}`, {
      method: options.method || 'GET',
      body: options.body,
      query: options.query,
      headers,
    })
  }

  return { request }
}
