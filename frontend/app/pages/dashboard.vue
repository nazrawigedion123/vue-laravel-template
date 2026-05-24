<script setup lang="ts">
import type { Language } from '~/types/api'

const api = useApi()
const auth = useAuth()
const mode = ref<'login' | 'register'>('login')
const email = ref('test@example.com')
const password = ref('123')
const title = ref('')
const content = ref('')
const languageId = ref<number | null>(null)
const message = ref('')
const errorMessage = ref('')

const { data: languages } = await useAsyncData('languages', () => api.request<Language[]>('/languages'))

watchEffect(() => {
  if (!languageId.value && languages.value?.length) {
    languageId.value = languages.value.find((language) => language.default)?.id || languages.value[0].id
  }
})

onMounted(() => {
  auth.loadUser().catch(() => null)
})

const authenticate = async () => {
  errorMessage.value = ''
  message.value = ''
  try {
    if (mode.value === 'login') {
      await auth.login(email.value, password.value)
    } else {
      await auth.register(email.value, password.value)
    }
    message.value = 'Signed in successfully.'
  } catch {
    errorMessage.value = 'Authentication failed. Check your email and password.'
  }
}

const createBlog = async () => {
  if (!auth.token.value || !languageId.value) return

  errorMessage.value = ''
  message.value = ''
  try {
    await api.request('/blogs', {
      method: 'POST',
      token: auth.token.value,
      body: {
        title: title.value,
        content: content.value,
        language_id: languageId.value,
      },
    })
    title.value = ''
    content.value = ''
    message.value = 'Blog created successfully.'
  } catch {
    errorMessage.value = 'Could not create the blog. Your user may need create permission.'
  }
}
</script>

<template>
  <main class="page">
    <section class="page-heading">
      <div>
        <p class="eyebrow">Dashboard</p>
        <h1>Create and manage content</h1>
      </div>
      <button v-if="auth.isAuthenticated.value" class="button secondary" type="button" @click="auth.logout">
        Logout
      </button>
    </section>

    <section class="dashboard-grid">
      <form class="panel" @submit.prevent="authenticate">
        <div class="tabs" role="tablist">
          <button type="button" :class="{ active: mode === 'login' }" @click="mode = 'login'">Login</button>
          <button type="button" :class="{ active: mode === 'register' }" @click="mode = 'register'">Register</button>
        </div>
        <label>
          Email
          <input v-model="email" class="field" type="email" autocomplete="email" required>
        </label>
        <label>
          Password
          <input v-model="password" class="field" type="password" autocomplete="current-password" required>
        </label>
        <button class="button" type="submit" :disabled="auth.loading.value">
          {{ mode === 'login' ? 'Login' : 'Register' }}
        </button>
        <p v-if="auth.user.value" class="success">Signed in as {{ auth.user.value.email }}</p>
      </form>

      <form class="panel" @submit.prevent="createBlog">
        <h2>Create blog</h2>
        <label>
          Language
          <select v-model="languageId" class="field" required>
            <option v-for="language in languages" :key="language.id" :value="language.id">
              {{ language.name }}
            </option>
          </select>
        </label>
        <label>
          Title
          <input v-model="title" class="field" maxlength="200" required>
        </label>
        <label>
          Content
          <textarea v-model="content" class="field" rows="10" required />
        </label>
        <button class="button" type="submit" :disabled="!auth.isAuthenticated.value || !auth.canCreateBlog.value">
          Create
        </button>
        <p v-if="auth.isAuthenticated.value && !auth.canCreateBlog.value" class="muted">
          This account is signed in but does not have blog creation permission.
        </p>
      </form>
    </section>

    <p v-if="message" class="success">{{ message }}</p>
    <p v-if="errorMessage" class="alert">{{ errorMessage }}</p>
  </main>
</template>
