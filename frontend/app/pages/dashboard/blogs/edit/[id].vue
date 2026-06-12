<template>
  <main class="max-w-6xl mx-auto pb-12">
    <!-- Sticky Header with Back Action -->
    <header class="sticky top-0 z-30 bg-surface/90 backdrop-blur-md pt-6 pb-4 px-4 sm:px-6 lg:px-8 border-b border-outline/20 mb-8 transition-all">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 max-w-5xl mx-auto">
        <div class="flex items-center gap-4">
          <NuxtLink 
            to="/dashboard/blogs" 
            class="p-2.5 rounded-full hover:bg-surface-variant/20 transition-colors text-surface-variant hover:text-primary"
            title="Back to blogs"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
          </NuxtLink>
          <div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-foreground">Edit Blog</h1>
            <p class="text-sm md:text-base text-surface-variant font-medium">Refine your story with granular language control.</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <button 
            class="h-11 px-6 bg-primary text-primary-foreground rounded-full font-bold shadow-sm hover:shadow-md hover:bg-primary/95 transition-all active:scale-[0.98] disabled:opacity-50"
            type="button" 
            :disabled="blogStore.isEditing" 
            @click="saveBlog"
          >
            <span v-if="!blogStore.isEditing">Save Blog</span>
            <span v-else class="flex items-center gap-2">
              <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Saving...
            </span>
          </button>
        </div>
      </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <DashboardMessage v-if="pageError" type="error" :message="pageError" class="mb-6" />
      
      <div v-if="!blog" class="flex flex-col items-center justify-center py-32 text-surface-variant">
        <svg class="animate-spin h-12 w-12 mb-6 text-primary/50" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <p class="font-bold text-lg tracking-wide">Loading blog masterpiece...</p>
      </div>

      <template v-else>
        <DashboardMessage v-if="blogStore.blogError" type="error" :message="blogStore.blogError" class="mb-6" />
        <DashboardMessage v-else-if="blogStore.blogSuccess" type="success" :message="blogStore.blogSuccess" class="mb-6" />

        <!-- Main Blog Details Card -->
        <section class="bg-surface rounded-[2rem] border border-outline shadow-sm overflow-hidden mb-12">
          <div class="p-6 md:p-8 border-b border-outline bg-surface-variant/5">
            <h2 class="text-xl font-bold text-foreground tracking-tight">Main Blog Details</h2>
            <p class="text-sm text-surface-variant font-medium mt-1">Core information and featured media.</p>
          </div>

          <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            <!-- Left Column: Media Upload -->
            <div class="lg:col-span-4 space-y-4">
              <div class="sticky top-32">
                <DashboardMediaUpload
                  v-model="blogMediaIds"
                  label="Featured Media"
                  hint="High-quality images or videos for the blog header."
                  :initial-media="blogMedia"
                />
              </div>
            </div>

            <!-- Right Column: M3 Tabs & Form -->
            <div class="lg:col-span-8">
              <!-- M3 Style Tabs -->
              <div class="flex overflow-x-auto border-b border-outline/30 mb-8 scrollbar-hide">
                <button 
                  class="relative px-6 py-3 text-sm font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                  :class="activeBlogTab === defaultLanguageCode ? 'text-primary' : 'text-surface-variant hover:text-foreground'"
                  @click="activeBlogTab = defaultLanguageCode"
                >
                  {{ defaultLanguageCode }}
                  <div v-if="activeBlogTab === defaultLanguageCode" class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div>
                </button>
                <button 
                  v-for="language in secondaryLanguages" 
                  :key="language.code"
                  class="relative px-6 py-3 text-sm font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                  :class="activeBlogTab === language.code ? 'text-primary' : 'text-surface-variant hover:text-foreground'"
                  @click="activeBlogTab = language.code"
                >
                  {{ language.code }}
                  <div v-if="activeBlogTab === language.code" class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div>
                </button>
              </div>

              <!-- Active Translation Inputs -->
              <div class="transition-all duration-300">
                <div v-if="blogTranslations[activeBlogTab]" class="space-y-6">
                  <M3TextField v-model="blogTranslations[activeBlogTab].title" :label="`Blog Title (${activeBlogTabName})`" />
                  <M3TextArea v-model="blogTranslations[activeBlogTab].summary" :label="`Short Summary (${activeBlogTabName})`" rows="3" />
                  <M3TextArea v-model="blogTranslations[activeBlogTab].content" :label="`Full Content (${activeBlogTabName})`" rows="12" />
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Sections Management -->
        <section class="space-y-6">
          <div class="flex items-center justify-between px-2 mb-8">
            <div>
              <h2 class="text-2xl font-black tracking-tight text-foreground">Content Sections</h2>
              <p class="text-surface-variant text-sm font-medium mt-1">Build your story visually and textually, segment by segment.</p>
            </div>
            <button 
              v-if="!isAddingSection" 
              class="h-11 px-6 bg-secondary text-secondary-foreground rounded-full font-bold shadow-sm hover:shadow-md transition-all active:scale-95 flex items-center gap-2"
              type="button" 
              @click="openAddSection"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              Add Section
            </button>
          </div>

          <!-- Add Section Form (Elevated Card) -->
          <div v-if="isAddingSection" class="bg-surface rounded-[2rem] border-2 border-primary/30 shadow-xl overflow-hidden transition-all duration-300 mb-8">
            <div class="p-6 md:px-8 md:py-6 border-b border-outline/30 bg-primary/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
              <h3 class="text-lg font-black text-primary uppercase tracking-widest flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                New Section
              </h3>
              
              <div class="flex items-center gap-3">
                <button class="h-10 px-5 text-surface-variant font-bold hover:text-foreground hover:bg-surface-variant/10 rounded-full transition-colors" type="button" @click="cancelAddSection">Cancel</button>
                <button 
                  class="h-10 px-6 bg-primary text-primary-foreground rounded-full font-bold shadow-sm hover:shadow-md transition-all active:scale-95" 
                  type="button" 
                  :disabled="blogStore.isCreating" 
                  @click="addSection"
                >
                  {{ blogStore.isCreating ? 'Saving...' : 'Save Section' }}
                </button>
              </div>
            </div>
            
            <div class="p-6 md:p-8">
              <!-- M3 Style Tabs for New Section -->
              <div class="flex overflow-x-auto border-b border-outline/30 mb-8 scrollbar-hide">
                <button 
                  class="relative px-6 py-3 text-sm font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                  :class="newSection.activeTab === defaultLanguageCode ? 'text-primary' : 'text-surface-variant hover:text-foreground'"
                  @click="newSection.activeTab = defaultLanguageCode"
                >
                  {{ defaultLanguageCode }}
                  <div v-if="newSection.activeTab === defaultLanguageCode" class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div>
                </button>
                <button 
                  v-for="language in secondaryLanguages" 
                  :key="language.code"
                  class="relative px-6 py-3 text-sm font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                  :class="newSection.activeTab === language.code ? 'text-primary' : 'text-surface-variant hover:text-foreground'"
                  @click="newSection.activeTab = language.code"
                >
                  {{ language.code }}
                  <div v-if="newSection.activeTab === language.code" class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div>
                </button>
              </div>

              <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left: Order & Media -->
                <div class="lg:col-span-4 space-y-6">
                  <M3TextField v-model.number="newSection.order" label="Sort Order" type="number" min="1" />
                  <DashboardMediaUpload
                    :key="newSectionKey"
                    v-model="newSection.media_ids"
                    label="Section Visuals"
                    hint="Visuals specific to this segment."
                    :initial-media="newSection.medias"
                  />
                </div>

                <!-- Right: Active Language inputs -->
                <div class="lg:col-span-8">
                  <div v-if="newSection.translations[newSection.activeTab]" class="space-y-6">
                    <M3TextField v-model="newSection.translations[newSection.activeTab].title" :label="`Section Title (${languageNameForCode(newSection.activeTab)})`" />
                    <M3TextArea v-model="newSection.translations[newSection.activeTab].content" :label="`Section Content (${languageNameForCode(newSection.activeTab)})`" rows="8" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section List -->
          <div class="space-y-6">
            <article 
              v-for="section in sectionForms" 
              :key="section.id" 
              class="bg-surface rounded-3xl transition-all duration-300"
              :class="editingSectionId === section.id ? 'ring-2 ring-primary/20 border-2 border-primary shadow-xl my-8' : 'border border-outline hover:border-outline/80 shadow-sm hover:shadow-md'"
            >
              <!-- Section Header -->
              <div class="p-5 md:px-8 md:py-6 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-outline/30 bg-surface-variant/5 rounded-t-3xl">
                <div class="flex items-center gap-5">
                  <div class="h-12 w-12 bg-surface rounded-full border border-outline/50 flex items-center justify-center font-black text-xl text-primary shadow-sm">
                    {{ section.order }}
                  </div>
                  <div>
                    <h3 class="text-lg font-bold text-foreground truncate max-w-xs md:max-w-md">
                      {{ section.translations[section.activeTab]?.title || 'Untitled Segment' }}
                    </h3>
                    <p class="text-xs text-surface-variant font-bold uppercase tracking-widest mt-1">{{ languageNameForCode(section.activeTab) }} Translation</p>
                  </div>
                </div>
                
                <div class="flex items-center gap-3">
                  <template v-if="editingSectionId === section.id">
                    <button class="h-10 px-5 text-surface-variant font-bold hover:text-foreground hover:bg-surface-variant/10 rounded-full transition-colors" type="button" @click="cancelEditSection">Cancel</button>
                    <button class="h-10 px-6 bg-primary text-primary-foreground rounded-full font-bold shadow-sm hover:shadow-md transition-all active:scale-[0.98]" type="button" :disabled="blogStore.isEditing" @click="saveSection(section)">
                      {{ blogStore.isEditing ? 'Saving...' : 'Update Section' }}
                    </button>
                  </template>
                  <template v-else>
                    <button class="h-10 px-5 text-primary font-bold hover:bg-primary/10 rounded-full transition-colors" type="button" @click="editSection(section)">Edit</button>
                    <button class="h-10 w-10 flex items-center justify-center text-error hover:bg-error/10 rounded-full transition-colors" type="button" :disabled="blogStore.isLoading" @click="deleteSection(section)" title="Delete Section">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                  </template>
                </div>
              </div>

              <div class="p-6 md:p-8">
                <!-- M3 Style Tabs for Existing Section (Only visible if Editing OR viewing multi-lang preview) -->
                <div class="flex overflow-x-auto border-b border-outline/30 mb-8 scrollbar-hide">
                  <button 
                    class="relative px-5 py-2.5 text-sm font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                    :class="section.activeTab === defaultLanguageCode ? 'text-primary' : 'text-surface-variant hover:text-foreground'"
                    @click="section.activeTab = defaultLanguageCode"
                  >
                    {{ defaultLanguageCode }}
                    <div v-if="section.activeTab === defaultLanguageCode" class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div>
                  </button>
                  <button 
                    v-for="language in secondaryLanguages" 
                    :key="language.code"
                    class="relative px-5 py-2.5 text-sm font-bold uppercase tracking-wider transition-colors whitespace-nowrap"
                    :class="section.activeTab === language.code ? 'text-primary' : 'text-surface-variant hover:text-foreground'"
                    @click="section.activeTab = language.code"
                  >
                    {{ language.code }}
                    <div v-if="section.activeTab === language.code" class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-full"></div>
                  </button>
                </div>

                <!-- EDIT MODE -->
                <div v-if="editingSectionId === section.id" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                  <!-- Left: Order & Media -->
                  <div class="lg:col-span-4 space-y-6">
                    <M3TextField v-model.number="section.order" label="Sort Order" type="number" min="1" />
                    <DashboardMediaUpload
                      v-model="section.media_ids"
                      label="Section Visuals"
                      hint="Update images for this part of the blog."
                      :initial-media="section.medias"
                    />
                  </div>
                  <!-- Right: Form -->
                  <div class="lg:col-span-8">
                    <div v-if="section.translations[section.activeTab]" class="space-y-6">
                      <M3TextField v-model="section.translations[section.activeTab].title" :label="`Section Title (${languageNameForCode(section.activeTab)})`" />
                      <M3TextArea v-model="section.translations[section.activeTab].content" :label="`Section Content (${languageNameForCode(section.activeTab)})`" rows="8" />
                    </div>
                  </div>
                </div>

                <!-- PREVIEW MODE -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                  <div v-if="section.medias.length" class="lg:col-span-4 flex flex-wrap gap-3">
                    <img v-for="media in section.medias" :key="media.id" :src="media.url" :alt="media.filename" class="h-32 w-full object-cover rounded-2xl border border-outline/50 shadow-sm">
                  </div>
                  <div :class="section.medias.length ? 'lg:col-span-8' : 'lg:col-span-12'">
                    <p class="text-base text-foreground/90 leading-relaxed font-medium whitespace-pre-line">
                      {{ section.translations[section.activeTab]?.content || `No content available in ${languageNameForCode(section.activeTab)}.` }}
                    </p>
                  </div>
                </div>

              </div>
            </article>

            <!-- Empty State -->
            <div v-if="!sectionForms.length" class="flex flex-col items-center justify-center py-24 bg-surface-variant/5 rounded-[2rem] border-2 border-dashed border-outline/50 transition-colors hover:border-primary/30 group">
              <div class="h-20 w-20 bg-surface rounded-full shadow-sm flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="h-10 w-10 text-primary/60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
              </div>
              <p class="font-black text-foreground text-lg mb-2">No Content Sections</p>
              <p class="text-surface-variant font-medium text-sm mb-6 text-center max-w-sm">Break your blog down into readable segments with unique images and text.</p>
              <button class="px-8 py-3 bg-primary/10 text-primary rounded-full font-bold hover:bg-primary/20 transition-all active:scale-95" @click="openAddSection">Create First Section</button>
            </div>
          </div>
        </section>
      </template>
    </div>
  </main>
</template>

<script setup lang="ts">
import { h, defineComponent, reactive, ref, computed, watch } from 'vue'
import DashboardMediaUpload from '~/components/dashboard/MediaUpload.vue'
import DashboardMessage from '~/components/dashboard/DashboardMessage.vue'
import type { Blog, Section, Translation, TranslationItem } from '~/types/blog'
import type { Language } from '~/types/language'
import type { Media } from '~/types/media'
import { useBlogStore } from '~/store/blogStore'
import { useLanguageStore } from '~/store/languageStore'
import { useLanguages } from '~/composables/languages'

// Internal UI Components using Render Functions for runtime compatibility
const M3TextField = defineComponent({
  props: ['modelValue', 'label', 'type', 'min'],
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    return () => h('div', { class: 'group relative' }, [
      h('input', {
        value: props.modelValue,
        onInput: (e: Event) => emit('update:modelValue', (e.target as HTMLInputElement).value),
        type: props.type || 'text',
        min: props.min,
        placeholder: ' ',
        class: 'peer w-full px-4 py-4 bg-transparent border border-outline rounded-xl transition-all duration-200 outline-none focus:ring-2 focus:ring-primary focus:border-primary disabled:opacity-50 text-foreground'
      }),
      h('label', {
        class: 'absolute left-4 px-1 -top-2.5 bg-surface text-xs font-bold text-surface-variant transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:font-medium peer-placeholder-shown:text-surface-variant/60 peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-primary peer-focus:font-bold pointer-events-none'
      }, props.label)
    ])
  }
})

const M3TextArea = defineComponent({
  props: ['modelValue', 'label', 'rows'],
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    return () => h('div', { class: 'group relative' }, [
      h('textarea', {
        value: props.modelValue,
        onInput: (e: Event) => emit('update:modelValue', (e.target as HTMLTextAreaElement).value),
        rows: props.rows || 4,
        placeholder: ' ',
        class: 'peer w-full px-4 py-4 bg-transparent border border-outline rounded-xl transition-all duration-200 outline-none focus:ring-2 focus:ring-primary focus:border-primary resize-none disabled:opacity-50 text-foreground'
      }),
      h('label', {
        class: 'absolute left-4 px-1 -top-2.5 bg-surface text-xs font-bold text-surface-variant transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:font-medium peer-placeholder-shown:text-surface-variant/60 peer-focus:-top-2.5 peer-focus:text-xs peer-focus:text-primary peer-focus:font-bold pointer-events-none'
      }, props.label)
    ])
  }
})

definePageMeta({
  layout: 'dashboard',
  middleware: ['admin'],
})

type TranslationForm = {
  language_id: number
  language_code: string
  title: string
  summary: string
  content: string
}

type SectionForm = {
  id?: number
  order: number
  media_ids: number[]
  medias: MediaPreview[]
  translations: Record<string, TranslationForm>
  activeTab: string
}

type MediaPreview = Pick<Media, 'id' | 'filename' | 'url'> & Partial<Media>

const route = useRoute()
const api = useApi()
const blogStore = useBlogStore()
const languageStore = useLanguageStore()
const blogId = computed(() => Number(route.params.id))

const { data: languagesData } = await useLanguages({
  onResponse({ response }) {
    if (response._data) {
      backendLanguageCodes.value = new Set(response._data.map((l: Language) => l.code))
    }
  }
})

const languages = computed(() => languagesData.value || [])
const defaultLanguageCode = computed(() => languageStore.currentLanguagePreference || 'en')

const blog = ref<Blog | null>(null)
const blogMediaIds = ref<number[]>([])
const blogMedia = ref<MediaPreview[]>([])
const blogTranslations = reactive<Record<string, TranslationForm>>({})
const sectionForms = ref<SectionForm[]>([])
const pageError = ref<string | null>(null)
const activeBlogTab = ref<string>(defaultLanguageCode.value)
const isAddingSection = ref(false)
const editingSectionId = ref<number | null>(null)
const newSectionKey = ref(0)
const backendLanguageCodes = ref<Set<string>>(new Set())

const secondaryLanguages = computed(() => languages.value.filter((language) => language.code !== defaultLanguageCode.value))

const languageNameForCode = (code: string) => {
  return languages.value.find(l => l.code === code)?.name || code
}

const activeBlogTabName = computed(() => languageNameForCode(activeBlogTab.value))

const emptyTranslation = (language: Language): TranslationForm => ({
  language_id: language.id,
  language_code: language.code,
  title: '',
  summary: '',
  content: '',
})

const mergeTranslation = (translation: Partial<Translation>, language: Language): TranslationForm => {
  return {
    language_id: translation.language_id || language.id,
    language_code: translation.language_code || language.code,
    title: translation.title || '',
    summary: translation.summary || '',
    content: translation.content || '',
  }
}

const ensureTranslationRecord = (
  translations: Array<Partial<Translation>>,
): Record<string, TranslationForm> => {
  const record: Record<string, TranslationForm> = {}

  for (const language of languages.value) {
    record[language.code] = emptyTranslation(language)
  }

  for (const translation of translations) {
    const code = translation.language_code || defaultLanguageCode.value
    const language = languages.value.find(l => l.code === code)
    if (language) {
      record[code] = mergeTranslation(translation, language)
    }
  }
  
  return record
}

const asPayloadTranslations = (translations: Record<string, TranslationForm>): Translation[] => {
  const defCode = defaultLanguageCode.value
  const defaultTranslation = translations[defCode]
  if (!defaultTranslation) return []

  const optionalTranslations = Object.values(translations).filter((translation) => {
    if (translation.language_code === defCode) return false
    if (!backendLanguageCodes.value.has(translation.language_code)) return false

    return Boolean(
      translation.title.trim() ||
      translation.summary.trim() ||
      translation.content.trim(),
    )
  })

  return [defaultTranslation, ...optionalTranslations]
    .map((translation) => ({
      language_id: translation.language_id,
      language_code: translation.language_code,
      title: translation.title.trim(),
      summary: translation.summary.trim(),
      content: translation.content.trim() || defaultTranslation.content.trim(),
    }))
}

const asSectionTranslations = (translations: Record<string, TranslationForm>): TranslationItem[] => {
  const defCode = defaultLanguageCode.value
  const defaultTranslation = translations[defCode]
  if (!defaultTranslation) return []

  const optionalTranslations = Object.values(translations).filter((translation) => {
    if (translation.language_code === defCode) return false
    if (!backendLanguageCodes.value.has(translation.language_code)) return false

    return Boolean(translation.title.trim() || translation.content.trim())
  })

  return [defaultTranslation, ...optionalTranslations]
    .map((translation) => ({
      language_id: translation.language_id,
      language_code: translation.language_code,
      title: translation.title.trim(),
      content: translation.content.trim() || defaultTranslation.content.trim(),
    }))
}

const toMediaPreviews = (media: unknown): MediaPreview[] => {
  if (!Array.isArray(media)) return []

  return media
    .filter((item): item is MediaPreview => {
      return Boolean(item && typeof item === 'object' && 'id' in item && 'url' in item)
    })
    .map((item) => ({
      id: Number(item.id),
      filename: String(item.filename || `media-${item.id}`),
      url: String(item.url || ''),
      mime_type: String(item.mime_type || ''),
    }))
}

const makeSectionForm = (section?: Section): SectionForm => {
  const medias = toMediaPreviews(section?.medias)

  return {
    id: section?.id,
    order: section?.order || sectionForms.value.length + 1,
    media_ids: medias.map((media) => media.id),
    medias,
    translations: ensureTranslationRecord(section?.translations || []),
    activeTab: defaultLanguageCode.value
  }
}

const resetNewSection = () => {
  newSection.value = makeSectionForm()
  newSectionKey.value += 1
}

const newSection = ref<SectionForm>(makeSectionForm())

const hydrateFromBlog = (loadedBlog: Blog) => {
  blog.value = loadedBlog
  const media = toMediaPreviews((loadedBlog as Blog & { medias?: MediaPreview[] }).medias || loadedBlog.media_ids)

  blogMedia.value = media
  blogMediaIds.value = media.map((item) => item.id)

  const nextTranslations = ensureTranslationRecord(loadedBlog.translations || [])
  Object.keys(blogTranslations).forEach((key) => delete blogTranslations[key])
  Object.assign(blogTranslations, nextTranslations)

  activeBlogTab.value = defaultLanguageCode.value
  
  sectionForms.value = [...(loadedBlog.sections || [])]
    .sort((left, right) => left.order - right.order)
    .map((section) => makeSectionForm(section))
  
  resetNewSection()
}

const loadBlog = async () => {
  const id = blogId.value
  if (!Number.isFinite(id)) {
    pageError.value = 'Invalid blog id.'
    return
  }

  const loadedBlog = await blogStore.getBlogById(id, true)

  if (!loadedBlog) {
    pageError.value = blogStore.blogError || 'Could not load this blog.'
    return
  }

  hydrateFromBlog(loadedBlog)
}

const saveBlog = async () => {
  const updatedBlog = await blogStore.editBlog({
    translations: asPayloadTranslations(blogTranslations),
    media_ids: blogMediaIds.value,
  }, blogId.value)

  if (updatedBlog) {
    await loadBlog()
  }
}

const openAddSection = () => {
  editingSectionId.value = null
  isAddingSection.value = true
}

const cancelAddSection = () => {
  isAddingSection.value = false
  resetNewSection()
}

const addSection = async () => {
  const updatedBlog = await blogStore.createBlogSection(blogId.value, {
    order: newSection.value.order,
    translations: asSectionTranslations(newSection.value.translations),
    media_ids: newSection.value.media_ids,
  })

  if (updatedBlog) {
    isAddingSection.value = false
    hydrateFromBlog(updatedBlog)
  }
}

const editSection = (section: SectionForm) => {
  isAddingSection.value = false
  editingSectionId.value = section.id || null
}

const cancelEditSection = async () => {
  editingSectionId.value = null
  await loadBlog()
}

const saveSection = async (section: SectionForm) => {
  if (!section.id) return

  const updatedBlog = await blogStore.editBlogSection(blogId.value, section.id, {
    order: section.order,
    translations: asSectionTranslations(section.translations),
    media_ids: section.media_ids,
  })

  if (updatedBlog) {
    editingSectionId.value = null
    hydrateFromBlog(updatedBlog)
  }
}

const deleteSection = async (section: SectionForm) => {
  if (!section.id) return

  const title = section.translations[defaultLanguageCode.value]?.title || 'this section'
  const confirmed = confirm(`Delete ${title}?`)

  if (!confirmed) return

  const success = await blogStore.removeBlogSection(blogId.value, section.id)

  if (success) {
    sectionForms.value = sectionForms.value.filter((item) => item.id !== section.id)
  }
}

watch(blogId, (newId) => {
  if (Number.isFinite(newId)) {
    loadBlog()
  }
})

// Initialize
if (languages.value.length === 0) {
    backendLanguageCodes.value = new Set(languages.value.map(l => l.code))
}

await loadBlog()
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
