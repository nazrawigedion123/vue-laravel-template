// // frontend/app/composables/useApi.ts
// import { type RequestOptions } from "~/types/api"
// import { useAuthStore } from "~/store/authStore"

// export const useApi = () => {
//   const auth=useAuthStore();
//   const{token}=auth;
//   const config = useRuntimeConfig()
//   const baseUrl = String(config.public.apiBase).replace(/\/$/, '')

//   const request = async <T>(path: string, options: RequestOptions = {}): Promise<T> => {
//     // Leverage native $fetch configuration merging
//     const headers: Record<string, string> = {
//       Accept: 'application/json',
//       ...((options.body && !(options.body instanceof FormData) && { 'Content-Type': 'application/json' }) || {}),
//       ...((options.token && { Authorization: `Bearer ${options.token}` }) || (auth.token && { Authorization: `Bearer ${auth.token}` }) ||{}),
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
import { useAuthStore } from "~/store/authStore"

export const useApi = () => {
  const auth = useAuthStore();
  const config = useRuntimeConfig()
  const baseUrl = String(config.public.apiBase).replace(/\/$/, '')

  const request = async <T>(path: string, options: RequestOptions = {}): Promise<T> => {
    // Token priority: options.token > auth.token > null
    const token = options.token !== undefined 
      ? options.token 
      : (auth.token || null);
    
    // Build headers with proper priority
    const headers: Record<string, string> = {
      Accept: 'application/json',
    };

    // Add Authorization header if token exists
    if (token) {
      headers.Authorization = `Bearer ${token}`;
    }

    // Set Content-Type header
    // Priority: Don't set for FormData (browser will set it with boundary)
    if (options.body && options.body instanceof FormData) {
      // Let browser set the Content-Type with boundary for FormData
      delete headers['Content-Type'];
    } else if (options.body && typeof options.body === 'object') {
      // For JSON bodies, set Content-Type to application/json
      headers['Content-Type'] = 'application/json';
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