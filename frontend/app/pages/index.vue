<script setup lang="ts">
import type { BlogSummary } from '~/types/api'

const api = useApi()
const lang = ref('en')

const { data: blogs, pending, error, refresh } = await useAsyncData(
  'blogs',
  () => api.request<BlogSummary[]>('/blogs', { query: { lang: lang.value } }),
  { watch: [lang] },
)
</script>

<template>
  <div class="container">
    <main class="page">
      <section class="page-heading">
        <div>
          <p class="eyebrow">Explore Our Content</p>
          <h1>Latest Blog Posts</h1>
        </div>
        <div class="filter-actions">
          <select v-model="lang" class="lang-select" aria-label="Language">
            <option value="en">English</option>
            <option value="am">Amharic</option>
          </select>
        </div>
      </section>

      <div v-if="pending" class="loading-state">
        <div class="spinner"></div>
        <p>Loading blogs...</p>
      </div>
      
      <div v-else-if="error" class="error-alert">
        <p>Could not load blogs. Please ensure the backend server is running.</p>
        <button @click="refresh" class="btn btn-outline">Retry</button>
      </div>

      <section v-else class="blog-grid">
        <article v-for="blog in blogs" :key="blog.id" class="blog-card">
          <div class="blog-card-content">
            <p class="meta">Published by {{ blog.author }}</p>
            <h2>{{ blog.title }}</h2>
            <p class="summary">{{ blog.summary }}</p>
          </div>
          <footer class="card-footer">
            <div class="stats">
              <span>💬 {{ blog.comment_count }}</span>
              <span>❤️ {{ blog.reaction_count }}</span>
            </div>
            <NuxtLink class="btn btn-secondary" :to="`/blogs/${blog.id}`">Read More</NuxtLink>
          </footer>
        </article>
        
        <div v-if="blogs?.length === 0" class="empty-state">
          <div class="empty-icon">📂</div>
          <h2>No blogs found</h2>
          <p>Be the first to share something amazing!</p>
          <NuxtLink v-if="hasDashboardAccess" to="/dashboard/blogs" class="btn btn-primary">Create Post</NuxtLink>
        </div>
      </section>
    </main>
  </div>
</template>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.page-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 3rem;
  border-bottom: 2px solid #f1f5f9;
  padding-bottom: 1.5rem;
}

.eyebrow {
  color: #3b82f6;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1e293b;
}

.lang-select {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background-color: white;
  font-weight: 500;
  color: #475569;
}

.blog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 2rem;
}

.blog-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: transform 0.2s, box-shadow 0.2s;
}

.blog-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.05);
}

.blog-card-content {
  padding: 1.5rem;
  flex: 1;
}

.meta {
  font-size: 0.8125rem;
  color: #94a3b8;
  margin-bottom: 0.75rem;
}

h2 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 1rem;
  line-height: 1.4;
}

.summary {
  color: #64748b;
  font-size: 0.9375rem;
  line-height: 1.6;
}

.card-footer {
  padding: 1.25rem 1.5rem;
  background-color: #f8fafc;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #f1f5f9;
}

.stats {
  display: flex;
  gap: 1rem;
  color: #64748b;
  font-size: 0.875rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.btn-secondary {
  background-color: #3b82f6;
  color: white;
}

.btn-secondary:hover {
  background-color: #2563eb;
}

.empty-state {
  grid-column: 1 / -1;
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 16px;
  border: 2px dashed #e2e8f0;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.loading-state {
  text-align: center;
  padding: 4rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
