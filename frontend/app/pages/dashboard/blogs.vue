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
  color: #1e293b;
}

.title-area p {
  color: #64748b;
}

.btn-primary {
  background-color: #2563eb;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}

.table-container {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.blog-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.blog-table th {
  background-color: #f8fafc;
  padding: 1rem 1.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #64748b;
  border-bottom: 1px solid #e2e8f0;
}

.blog-table td {
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.9375rem;
  color: #334155;
}

.blog-title {
  font-weight: 600;
  color: #1e293b;
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
  background-color: #f1f5f9;
  color: #475569;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.icon-btn {
  padding: 0.5rem;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
  background: white;
  cursor: pointer;
  font-size: 0.75rem;
}

.icon-btn:hover {
  background-color: #f8fafc;
}

.icon-btn.delete {
  color: #ef4444;
}
</style>
