<template>
  <div class="callback-page">
    <div class="status-card">
      <div v-if="error" class="error-view">
        <div class="icon">⚠️</div>
        <h1>Authentication Failed</h1>
        <p>{{ error }}</p>
        <NuxtLink to="/login" class="btn btn-primary">Back to Login</NuxtLink>
      </div>
      <div v-else class="loading-view">
        <div class="spinner"></div>
        <h1>Authenticating...</h1>
        <p>Completing the secure handshake with Google.</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()
const { handleGoogleCallback } = useAuth()

const error = ref('')

onMounted(async () => {
  const code = route.query.code as string
  
  if (!code) {
    error.value = 'No authorization code received from Google.'
    return
  }

  try {
    await handleGoogleCallback(code)
    router.push('/')
  } catch (err: any) {
    console.error('Google callback error:', err)
    error.value = err.message || 'An unexpected error occurred during Google sign-in.'
  }
})
</script>

<style scoped>
.callback-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
}

.status-card {
  background: white;
  padding: 3rem;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  text-align: center;
  max-width: 440px;
  width: 100%;
}

.loading-view h1, .error-view h1 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.loading-view p, .error-view p {
  color: #64748b;
  margin-bottom: 2rem;
}

.spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1.5rem;
}

.error-view .icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  text-decoration: none;
  display: inline-block;
  font-weight: 600;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
