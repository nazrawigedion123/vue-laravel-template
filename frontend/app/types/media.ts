
type MimeType = 'image/png' | 'image/jpeg' | 'image/jpg' | 'image/gif' | 'image/webp';

export interface MediaItem {
  id: number;
  filename: string;
  mime_type: string;
  url: string;
  user_id: number;
  created_at: string;
  updated_at: string;
}
export type MediaItems = MediaItem[];

export type Media=MediaItem;


export interface UploadMediaResponse {
    message: string;
    data: Media;
}

