export default defineNuxtRouteMiddleware((to, from) => {
  const { isAuthenticated, hasDashboardAccess } = useAuth()

  if (!isAuthenticated.value) {
    return navigateTo('/login')
  }

  if (!hasDashboardAccess.value) {
    return navigateTo('/')
  }
})
