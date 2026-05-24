type RequestOptions = {
  method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE'
  body?: any
  token?: string | null
  query?: Record<string, string | number | boolean | undefined>
}

export const useApi = () => {
  const config = useRuntimeConfig()
  const baseUrl = String(config.public.apiBase).replace(/\/$/, '')

  const request = async <T>(path: string, options: RequestOptions = {}) => {
    const headers: HeadersInit = {
      Accept: 'application/json',
    }

    if (options.body) {
      headers['Content-Type'] = 'application/json'
    }

    if (options.token) {
      headers.Authorization = `Bearer ${options.token}`
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
