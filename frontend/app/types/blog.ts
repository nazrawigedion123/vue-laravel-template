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
    medias: Media[]
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


export interface BlogCreateResponse {
    message: string;
    blog_id:number;
}



export type BlogEditRequest =BlogCreateRequest; 





// types/blog.ts

export interface Media {
    id: number;
    filename: string;
    mime_type: string;
    url: string;
}



export interface BlogItem {
    id: number;
    author: string;
    published_at: string | null;
    reaction_count: number;
    comment_count: number;
    medias: Media[];
    translations: Translation[];
}



export interface BlogEditResponse{
    message: string;
    blog: BlogItem;

};




export interface AddBlogSectionRequest {
  order: number;                    // required, integer
  translations: TranslationItem[];  // required, array with at least 1 item
  media_ids?: number[];             // optional, array of media IDs
  // image?: File;                  // commented out in your backend, but if uncommented later
}

export interface TranslationItem {
  language_id: number;   // required, must exist in languages table
  language_code:string;
  title: string;         // required, max 200 characters
  content: string;       // required
}




export interface AddBlogSectionResponse {
  message: string;
  section: Section;
}

export type EditBlogSectionRequest=AddBlogSectionRequest;
export type EditBlogSectionResponse=AddBlogSectionResponse;

