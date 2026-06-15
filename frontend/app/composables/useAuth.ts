import {storeToRefs} from 'pinia'
import { useAuthStore } from '~/store/authStore'
export const useAuth=()=>{
  const authStore =useAuthStore()

  const{token,user,loading,isAuthenticated,isSuperuser,isStaff,hasDashboardAccess,canCreateBlog,}=storeToRefs(authStore)

  const {loadUser,login,register,loginWithGoogle,handleGoogleCallback}=authStore

  const logout = async () => {
    await authStore.logout()
    return navigateTo('/')
  }

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