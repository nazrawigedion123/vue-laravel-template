<script setup lang="ts">
import type { BlogDetail, ReactionType } from '~/types/api'

import { usePageStore } from '~/store/pageStore'

definePageMeta({
  middleware: [
    () => {
      usePageStore().setCurrentPage('blog')
    }
  ]
})

const route = useRoute()
const api = useApi()
const auth = useAuth()
const lang = ref('en')
const comment = ref('')
const status = ref('')
const reactionTypes: ReactionType[] = ['like', 'love', 'haha', 'wow', 'sad', 'angry']

const { data: blog, pending, error, refresh } = await useAsyncData(
  () => `blog-${route.params.id}-${lang.value}`,
  () => api.request<BlogDetail>(`/blogs/${route.params.id}`, { query: { lang: lang.value } }),
  { watch: [lang] },
)

const submitComment = async () => {
  if (!auth.token.value || !comment.value.trim()) return

  await api.request(`/blogs/${route.params.id}/comment`, {
    method: 'POST',
    token: auth.token.value,
    body: { content: comment.value.trim() },
  })
  comment.value = ''
  status.value = 'Comment posted.'
  await refresh()
}

const react = async (reaction_type: ReactionType) => {
  if (!auth.token.value) return

  await api.request(`/blogs/${route.params.id}/react`, {
    method: 'POST',
    token: auth.token.value,
    body: { reaction_type },
  })
  status.value = 'Reaction updated.'
  await refresh()
}
</script>

<template>
  <main class="page narrow">
    <NuxtLink class="text-link" to="/">Back to blogs</NuxtLink>

    <p v-if="pending" class="muted">Loading blog...</p>
    <p v-else-if="error" class="alert">Could not load this blog.</p>

    <article v-else-if="blog" class="article">
      <section class="page-heading">
        <div>
          <p class="eyebrow">By {{ blog.author }}</p>
          <h1>{{ blog.title }}</h1>
        </div>
        <select v-model="lang" class="field compact" aria-label="Language">
          <option value="en">English</option>
          <option value="am">Amharic</option>
        </select>
      </section>

      <p class="article-content">{{ blog.content }}</p>

      <section v-if="blog.sections.length" class="section-list">
        <article v-for="section in blog.sections" :key="section.id" class="content-section">
          <img v-if="section.image" :src="section.image" :alt="section.title">
          <h2>{{ section.title }}</h2>
          <p>{{ section.content }}</p>
        </article>
      </section>

      <section class="interaction-panel">
        <div>
          <h2>Reactions</h2>
          <p class="muted">{{ blog.reaction_count }} total</p>
        </div>
        <div class="reaction-row">
          <button
            v-for="reaction in reactionTypes"
            :key="reaction"
            class="button secondary"
            type="button"
            :disabled="!auth.isAuthenticated.value"
            @click="react(reaction)"
          >
            {{ reaction }}
          </button>
        </div>
      </section>

      <section class="interaction-panel">
        <h2>Comments</h2>
        <form v-if="auth.isAuthenticated.value" class="comment-form" @submit.prevent="submitComment">
          <textarea v-model="comment" class="field" rows="4" placeholder="Write a comment" />
          <button class="button" type="submit">Post comment</button>
        </form>
        <p v-else class="muted">Log in from the dashboard to comment or react.</p>
        <p v-if="status" class="success">{{ status }}</p>

        <div class="comment-list">
          <article v-for="item in blog.comments" :key="item.id" class="comment">
            <strong>{{ item.user }}</strong>
            <p>{{ item.content }}</p>
          </article>
          <p v-if="blog.comments.length === 0" class="muted">No comments yet.</p>
        </div>
      </section>
    </article>
  </main>
</template>
