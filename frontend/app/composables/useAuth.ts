// import type { AuthResponse, AuthUser } from '~/types/api'

// export const useAuth = () => {
//   const api = useApi()
//   const token = useCookie<string | null>('auth_token', {
//     sameSite: 'lax',
//     secure: false,
//     default: () => null,
//   })
//   const user = useState<AuthUser | null>('auth_user', () => null)
//   const loading = useState('auth_loading', () => false)

//   const isAuthenticated = computed(() => Boolean(token.value))
//   const canCreateBlog = computed(() => Boolean(user.value?.is_superuser || user.value?.role?.can_create_blog))

//   const loadUser = async () => {
//     if (!token.value) {
//       user.value = null
//       return null
//     }

//     user.value = await api.request<AuthUser>('/auth/me', { token: token.value })
//     return user.value
//   }

//   const login = async (email: string, password: string) => {
//     loading.value = true
//     try {
//       const response = await api.request<AuthResponse>('/auth/login', {
//         method: 'POST',
//         body: { email, password },
//       })
//       token.value = response.access_token
//       await loadUser()
//     } finally {
//       loading.value = false
//     }
//   }

//   const register = async (email: string, password: string) => {
//     loading.value = true
//     try {
//       const response = await api.request<AuthResponse>('/auth/register', {
//         method: 'POST',
//         body: { email, password },
//       })
//       token.value = response.access_token
//       await loadUser()
//     } finally {
//       loading.value = false
//     }
//   }

//   const logout = async () => {
//     if (token.value) {
//       await api.request('/auth/logout', { method: 'POST', token: token.value }).catch(() => null)
//     }
//     token.value = null
//     user.value = null
//   }

//   return {
//     token,
//     user,
//     loading,
//     isAuthenticated,
//     canCreateBlog,
//     loadUser,
//     login,
//     register,
//     logout,
//   }
// }

import {storeToRefs} from 'pinia'
import { useAuthStore } from '~/store/auth'
export const useAuth=()=>{
  const authStore =useAuthStore()

  const{token,user,loading,isAuthenticated,isSuperuser,isStaff,hasDashboardAccess,canCreateBlog,}=storeToRefs(authStore)

  const {loadUser,login,register,logout,loginWithGoogle,handleGoogleCallback}=authStore

  return{
    token,
    user,
    loading,
    isAuthenticated,
    isSuperuser,
    isStaff,
    hasDashboardAccess,
    canCreateBlog,
    loadUser,
    login,
    register,
    logout,
    loginWithGoogle,
    handleGoogleCallback
  }
}