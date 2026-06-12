<template>
  <div class="max-w-2xl mx-auto py-8">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <NuxtLink 
        to="/dashboard/blogs" 
        class="p-2 rounded-full hover:bg-surface-variant/20 transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      </NuxtLink>
      <h1 class="text-3xl font-extrabold tracking-tight text-foreground">Create New Blog</h1>
    </div>

    <!-- Main Card -->
    <div class="bg-surface rounded-3xl shadow-sm border border-outline p-6 md:p-8">
      
      <!-- Error Banner -->
      <DashboardMessage v-if="blogStore.blogError" class="mb-6" type="error" :message="blogStore.blogError" />

      <form @submit.prevent="handleSubmit" class="space-y-8">
        <!-- Title Input (MD3 Outlined) -->
        <div class="group relative">
          <input 
            id="title"
            v-model="form.title"
            type="text"
            required
            :disabled="blogStore.isCreating"
            placeholder=" "
            class="peer w-full px-4 py-4 bg-transparent border border-outline rounded-xl transition-all duration-200 
                   outline-none focus:ring-2 focus:ring-primary focus:border-primary 
                   disabled:opacity-50 disabled:bg-surface-variant/5"
          />
          <label 
            for="title"
            class="absolute left-4 px-1 -top-2.5 bg-surface text-xs font-medium text-surface-variant transition-all duration-200
                   peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-surface-variant/60
                   peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-primary"
          >
            Blog Title (English)
          </label>
        </div>

        <!-- Summary Input (MD3 Outlined Textarea) -->
        <div class="group relative">
          <textarea 
            id="summary"
            v-model="form.summary"
            rows="4"
            required
            :disabled="blogStore.isCreating"
            placeholder=" "
            class="peer w-full px-4 py-4 bg-transparent border border-outline rounded-xl transition-all duration-200 
                   outline-none focus:ring-2 focus:ring-primary focus:border-primary resize-none
                   disabled:opacity-50 disabled:bg-surface-variant/5"
          ></textarea>
          <label 
            for="summary"
            class="absolute left-4 px-1 -top-2.5 bg-surface text-xs font-medium text-surface-variant transition-all duration-200
                   peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-surface-variant/60
                   peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-primary"
          >
            Summary / Short Description
          </label>
        </div>

        <!-- Submit Button (MD3 Filled Pill) -->
        <div class="pt-2">
          <button 
            type="submit"
            :disabled="blogStore.isCreating"
            class="relative w-full h-12 bg-primary text-primary-foreground rounded-full font-bold shadow-sm 
                   hover:shadow-md hover:bg-primary/95 active:scale-[0.98] transition-all duration-200 
                   flex items-center justify-center disabled:opacity-60 disabled:cursor-not-allowed disabled:active:scale-100"
          >
            <span v-if="!blogStore.isCreating">Publish & Continue</span>
            <div v-else class="flex items-center gap-3">
              <svg class="animate-spin h-5 w-5 text-primary-foreground" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Publishing...</span>
            </div>
          </button>
        </div>
      </form>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 text-center text-sm text-surface-variant">
      Step 1 of 2: Basic Information
    </div>
  </div>
</template>

<script setup lang="ts">
import { useBlogStore } from '~/store/blogStore'
import DashboardMessage from '~/components/dashboard/DashboardMessage.vue'

definePageMeta({
  layout: 'dashboard',
  middleware: ['auth']
})

const blogStore = useBlogStore()
const router = useRouter()

const form = reactive({
  title: '',
  summary: ''
})

const handleSubmit = async () => {
  const payload = {
    translations: [
      {
        language_id: 1, // Defaulting to English Source
        language_code: 'en',
        title: form.title,
        summary: form.summary,
        content: '' // Initial content is empty, to be edited later
      }
    ],
    media_ids: []
  }

  const blogId = await blogStore.createBlog(payload)

  if (blogId) {
    // Redirect to the target edit view on success
    navigateTo(`/dashboard/blogs/edit/${blogId}`)
  }
}
</script>

<style scoped>
/* Additional MD3 Micro-interactions */
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus {
  -webkit-text-fill-color: var(--color-on-surface);
  -webkit-box-shadow: 0 0 0px 1000px var(--bg-surface) inset;
  transition: background-color 5000s ease-in-out 0;
}

/* Custom Material Elevation transition */
.shadow-sm {
  box-shadow: 0px 1px 3px 1px rgba(0, 0, 0, 0.15), 0px 1px 2px 0px rgba(0, 0, 0, 0.3);
}

.hover\:shadow-md:hover {
  box-shadow: 0px 2px 6px 2px rgba(0, 0, 0, 0.15), 0px 1px 2px 0px rgba(0, 0, 0, 0.3);
}
</style>
