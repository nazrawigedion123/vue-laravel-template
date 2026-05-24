<template>
  <div class="blog-management">
    <div class="header">
      <div class="title-area">
        <h1>Blog Management</h1>
        <p>Create, edit, and manage your published articles.</p>
      </div>
      <button class="btn btn-primary">+ New Post</button>
    </div>

    <div class="table-container">
      <table class="blog-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Status</th>
            <th>Published</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="blog in blogs" :key="blog.id">
            <td class="blog-title">{{ blog.title }}</td>
            <td>{{ blog.author }}</td>
            <td>
              <span class="badge" :class="blog.published_at ? 'badge-success' : 'badge-draft'">
                {{ blog.published_at ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td>{{ blog.published_at || 'Never' }}</td>
            <td class="actions">
              <button class="icon-btn">Edit</button>
              <button class="icon-btn delete">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'dashboard',
  middleware: ['admin']
})

const api = useApi()

const { data: blogs } = await useAsyncData('dashboard-blogs', () => 
  api.request('/blogs')
)
</script>

<style scoped>
.blog-management {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.title-area h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--text-primary);
}

.title-area p {
  color: var(--text-muted);
}

.btn-primary {
  background-color: var(--color-primary);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.table-container {
  background: var(--card-bg);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.blog-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.blog-table th {
  background-color: var(--bg-surface);
  padding: 1rem 1.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-muted);
  border-bottom: 1px solid var(--border-color);
}

.blog-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid var(--border-color);
  font-size: 0.9375rem;
  color: var(--text-primary);
}

.blog-title {
  font-weight: 600;
  color: var(--text-primary);
}

.badge {
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
}

.badge-success {
  background-color: #dcfce7;
  color: #166534;
}

.badge-draft {
  background-color: var(--bg-surface);
  color: var(--text-muted);
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.icon-btn {
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid var(--border-color);
  background: var(--bg-surface);
  cursor: pointer;
  font-size: 0.75rem;
  color: var(--text-primary);
}

.icon-btn:hover {
  filter: brightness(0.9);
}

.icon-btn.delete {
  color: #ef4444;
}
</style>
