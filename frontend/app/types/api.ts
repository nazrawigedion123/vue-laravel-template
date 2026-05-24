export type UserRole = {
  can_create_blog: boolean
  can_edit_blog: boolean
}

export type AuthUser = {
  id: number
  email: string
  first_name?: string
  last_name?: string
  is_superuser?: boolean
  is_staff?: boolean
  role?: UserRole
}

export type AuthResponse = {
  access_token: string
  token_type: string
  expires_in: number
}

export type Language = {
  id: number
  name: string
  code: string
  default: boolean
}

export type BlogSummary = {
  id: number
  title: string
  summary: string
  comment_count: number
  reaction_count: number
  author: string
  published_at: string | null
}

export type BlogComment = {
  id: number
  user: string
  content: string
  reply_to_id?: number | null
  created_at: string
}

export type BlogSection = {
  id: number
  order: number
  image: string | null
  title: string
  content: string
}

export type BlogDetail = {
  id: number
  title: string
  content: string
  author: string
  published_at: string | null
  reaction_count: number
  sections: BlogSection[]
  comments: BlogComment[]
}

export type ReactionType = 'like' | 'love' | 'haha' | 'wow' | 'sad' | 'angry'
