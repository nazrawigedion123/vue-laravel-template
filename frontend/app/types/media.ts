
type MimeType = 'image/png' | 'image/jpeg' | 'image/jpg' | 'image/gif' | 'image/webp';

export interface MediaItem {
  id: number;
  filename: string;
  mime_type: string;
  url: string;
}
export type MediaItems = MediaItem[];

export type Media=MediaItem;