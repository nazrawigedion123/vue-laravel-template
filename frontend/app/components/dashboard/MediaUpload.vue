<template>
  <div class="media-upload">
    <div class="media-upload__header">
      <div>
        <p class="media-upload__label">{{ label }}</p>
        <p class="media-upload__hint">{{ hint }}</p>
      </div>
      <label class="media-upload__button">
        <span>{{ isUploading ? 'Uploading...' : 'Upload' }}</span>
        <input
          type="file"
          accept="image/*"
          :multiple="multiple"
          :disabled="isUploading"
          @change="handleFileChange"
        >
      </label>
    </div>

    <p v-if="error" class="media-upload__error">{{ error }}</p>

    <div v-if="uploadedMedia.length" class="media-upload__grid">
      <article v-for="media in uploadedMedia" :key="media.id" class="media-upload__item">
        <img v-if="media.url" :src="media.url" :alt="media.filename">
        <div v-else class="media-upload__placeholder">{{ media.filename }}</div>
        <button type="button" class="media-upload__remove" @click="removeMedia(media.id)">
          Remove
        </button>
      </article>
    </div>
  </div>
</template>

<script setup lang="ts">
import { uploadMedia } from '~/utils/media'
import type { Media } from '~/types/media'

type UploadPreview = Pick<Media, 'id' | 'filename' | 'url'> & Partial<Media>

const props = withDefaults(defineProps<{
  label?: string
  hint?: string
  modelValue?: number[]
  initialMedia?: UploadPreview[]
  multiple?: boolean
}>(), {
  label: 'Media',
  hint: 'Upload an image and use its media ID in the form.',
  modelValue: () => [],
  initialMedia: () => [],
  multiple: true,
})

const emit = defineEmits<{
  'update:modelValue': [ids: number[]]
  uploaded: [media: Media]
}>()

const isUploading = ref(false)
const error = ref<string | null>(null)
const uploadedMedia = ref<UploadPreview[]>([...props.initialMedia])

watch(
  () => props.initialMedia,
  (media) => {
    uploadedMedia.value = [...media]
  },
)

const emitIds = () => {
  emit('update:modelValue', uploadedMedia.value.map((media) => media.id))
}

const handleFileChange = async (event: Event) => {
  const input = event.target as HTMLInputElement
  const files = Array.from(input.files || [])

  if (!files.length) return

  isUploading.value = true
  error.value = null

  try {
    const uploaded: Media[] = []

    for (const file of files) {
      const media = await uploadMedia(file)

      if (media) {
        uploaded.push(media)
        emit('uploaded', media)
      }
    }

    uploadedMedia.value = props.multiple
      ? [...uploadedMedia.value, ...uploaded]
      : uploaded.slice(0, 1)
    emitIds()
  } catch {
    error.value = 'Could not upload media.'
  } finally {
    isUploading.value = false
    input.value = ''
  }
}

const removeMedia = (id: number) => {
  uploadedMedia.value = uploadedMedia.value.filter((media) => media.id !== id)
  emitIds()
}
</script>

<style scoped>
.media-upload {
  display: grid;
  gap: 0.875rem;
}

.media-upload__header {
  align-items: center;
  border: 1px dashed var(--border-color);
  border-radius: 8px;
  display: flex;
  gap: 1rem;
  justify-content: space-between;
  padding: 1rem;
}

.media-upload__label {
  color: var(--text-primary);
  font-weight: 700;
  margin: 0;
}

.media-upload__hint {
  color: var(--text-muted);
  font-size: 0.875rem;
  margin: 0.25rem 0 0;
}

.media-upload__button {
  align-items: center;
  background: var(--color-primary);
  border: 0;
  border-radius: 8px;
  color: var(--bg-primary);
  cursor: pointer;
  display: inline-flex;
  font-weight: 700;
  min-height: 2.5rem;
  padding: 0 1rem;
}

.media-upload__button input {
  display: none;
}

.media-upload__error {
  color: var(--color-error, var(--text-primary));
  font-weight: 700;
  margin: 0;
}

.media-upload__grid {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(auto-fill, minmax(8.5rem, 1fr));
}

.media-upload__item {
  border: 1px solid var(--border-color);
  border-radius: 8px;
  display: grid;
  gap: 0.5rem;
  overflow: hidden;
  padding: 0.5rem;
}

.media-upload__item img,
.media-upload__placeholder {
  aspect-ratio: 4 / 3;
  background: var(--bg-surface);
  border-radius: 6px;
  object-fit: cover;
  width: 100%;
}

.media-upload__placeholder {
  align-items: center;
  color: var(--text-muted);
  display: flex;
  font-size: 0.8125rem;
  justify-content: center;
  padding: 0.5rem;
}

.media-upload__remove {
  background: transparent;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  color: var(--text-primary);
  cursor: pointer;
  min-height: 2rem;
}

@media (max-width: 680px) {
  .media-upload__header {
    align-items: stretch;
    flex-direction: column;
  }

  .media-upload__button {
    justify-content: center;
  }
}
</style>
