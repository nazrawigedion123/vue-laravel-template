// utils/mediaService.ts
import { useApi } from '@/composables/useApi';
import { useAuthStore } from "@/store/authStore";
import type{
    Media,
    UploadMediaResponse
} from '@/types/media';

export const uploadMedia = async (file: File, filename?: string): Promise<Media | null> => {
    const api=useApi();
        //auth store 
    const auth=useAuthStore();
    const{token}=auth;
    const formData = new FormData();
    formData.append('file', file);
    if (filename) {
        formData.append('filename', filename);
    }

    try {
        const response = await useApi().request<UploadMediaResponse>('/media/upload', {
            method: 'POST',
            token: auth.token,
            body: formData,
        });
        
        return response.data;
    } catch (error) {
        console.error('Failed to upload media:', error);
        return null;
    }
};

export const uploadExternalMedia = async (externalUrl: string, filename?: string): Promise<Media | null> => {
     const api=useApi();
        //auth store 
    const auth=useAuthStore();
    const{token}=auth;
    try {
        const response = await useApi().request<UploadMediaResponse>('/media/upload', {
            method: 'POST',
            token: auth.token,
            body: {
                external_url: externalUrl,
                filename: filename
            }
        });
        
        return response.data;
    } catch (error) {
        console.error('Failed to save external media:', error);
        return null;
    }
};