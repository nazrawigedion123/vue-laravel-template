<template>
  <div class="auth-page">
    <div class="auth-card">
      <h1>Welcome Back</h1>
      <p class="subtitle">Please enter your details to sign in</p>

      <form @submit.prevent="handleLogin" class="auth-form">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input 
            v-model="form.email" 
            type="email" 
            id="email" 
            placeholder="name@company.com" 
            required
            :disabled="loading"
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            id="password" 
            placeholder="••••••••" 
            required
            :disabled="loading"
          />
        </div>

        <div v-if="error" class="error-message">
          {{ error }}
        </div>

        <button type="submit" class="btn-submit" :disabled="loading">
          <span v-if="loading">Signing in...</span>
          <span v-else>Sign In</span>
        </button>
      </form>

      <div class="divider">
        <span>OR</span>
      </div>

      <button @click="loginWithGoogle" class="btn-google" :disabled="loading">
        <img src="https://authjs.dev/img/providers/google.svg" alt="Google" width="20" height="20" />
        Continue with Google
      </button>

      <p class="footer-text">
        Don't have an account? <NuxtLink to="/register">Create one</NuxtLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
const { login, loginWithGoogle, loading } = useAuth()
const router = useRouter()

const form = reactive({
  email: '',
  password: ''
})

const error = ref('')

const handleLogin = async () => {
  error.value = ''
  try {
    await login(form.email, form.password)
    router.push('/')
  } catch (err) {
    error.value = 'Invalid email or password. Please try again.'
  }
}
</script>

<style scoped>
.auth-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
}

.auth-card {
  background: white;
  padding: 2.5rem;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
  width: 100%;
  max-width: 400px;
}

h1 {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  text-align: center;
}

.subtitle {
  color: #64748b;
  text-align: center;
  margin-bottom: 2rem;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #1e293b;
}

input {
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.btn-submit {
  background-color: #2563eb;
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-submit:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  background-color: #fef2f2;
  padding: 0.75rem;
  border-radius: 6px;
  border: 1px solid #fee2e2;
}

.divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 1.5rem 0;
  color: #94a3b8;
  font-size: 0.75rem;
  font-weight: 600;
}

.divider::before, .divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid #e2e8f0;
}

.divider span {
  padding: 0 1rem;
}

.btn-google {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  background-color: white;
  border: 1px solid #e2e8f0;
  padding: 0.75rem;
  border-radius: 8px;
  font-weight: 600;
  color: #1e293b;
  cursor: pointer;
  transition: background-color 0.2s;
  margin-bottom: 1rem;
}

.btn-google:hover:not(:disabled) {
  background-color: #f8fafc;
}

.footer-text {
  text-align: center;
  margin-top: 2rem;
  color: #64748b;
  font-size: 0.875rem;
}

.footer-text a {
  color: #2563eb;
  text-decoration: none;
  font-weight: 600;
}
</style>
