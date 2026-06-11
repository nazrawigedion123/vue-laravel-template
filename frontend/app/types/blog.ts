// types/blog.ts

import type { MediaItem,MediaItems } from "./media";

export interface Translation {
    language_id: number;
    language_code: string;
    title: string | null;
    summary?: string | null;  // For blog translations
    content: string | null;
}

export interface SectionTranslation {
    language_id: number;
    language_code: string;
    title: string | null;
    content: string | null;
}

export interface Section {
    id: number;
    order: number;
    image: string | null;
    translations: SectionTranslation[];
}

export interface Comment {
    id: number;
    user: string;
    content: string;
    reply_to_id: number | null;
    created_at: string; // or Date if you parse it
}


export interface Blog {
    id: number;
    comment_count: number;
    reaction_count: number;
    author: string;
    published_at: string | null;
    sections: Section[];
    comments: Comment[];
    media_ids: MediaItems;
    translations: Translation[];
}

export interface BlogsResponse {
    data: Blog[];
    currentPage:number;
    totalItems:number;
    totalPages:number;
}






export interface BlogCreateRequest {
  translations: Translation[];
  media_ids: number[];
}
export interface BlogEditRequest {
    id:number,
    translations: Translation[];
    media_ids: number[];
}
export interface BlogCreateResponse {
    message: string;
    blog_id:number;
}




 
