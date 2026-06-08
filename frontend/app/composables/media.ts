
// frontend/app/composables/media.ts
// frontend/app/composables/media.ts
import { ref } from "vue";
import { type Media } from "~/types/media";
import { useApi } from "#imports";

export const useMedia = () => {
  const api = useApi();
  
  const isUploading = ref(false);
  const isUpdating = ref(false);
  const isDeleting = ref(false);
  const mediaError = ref<string | null>(null);

  /**
   * Upload a physical file or submit an external media URL
   */
  const uploadMedia = async (payload: {
    file?: File | null;
    external_url?: string;
    filename?: string;
  }): Promise<{ message: string; data: Media } | null> => {
    isUploading.value = true;
    mediaError.value = null;

    try {
      let body: FormData | Record<string, any>;
      let headers: Record<string, string> = {};

      // If a physical file is present, we must send it as multipart/form-data
      if (payload.file) {
        const formData = new FormData();
        formData.append("file", payload.file);
        
        if (payload.filename) {
          formData.append("filename", payload.filename);
        }
        body = formData;
        // NOTE: Nuxt/ofetch automatically handles the multipart boundaries when passing FormData,
        // do not manually set 'Content-Type' to 'multipart/form-data'.
      } else {
        // Otherwise, send it as standard JSON for external links
        body = {
          external_url: payload.external_url,
          filename: payload.filename,
        };
      }

      const response = await api.request<{ message: string; data: Media }>("/media/upload", {
        method: "POST",
        body,
      });

      return response;
    } catch (err: any) {
      mediaError.value = err.data?.message || "Failed to upload media.";
      throw err;
    } finally {
      isUploading.value = false;
    }
  };

  /**
   * Update the filename/display name of a media item
   */
  const updateMedia = async (
    id: number,
    filename: string
  ): Promise<{ message: string; data: Media } | null> => {
    isUpdating.value = true;
    mediaError.value = null;

    try {
      const response = await api.request<{ message: string; data: Media }>(`/media/${id}`, {
        method: "PUT",
        body: { filename },
      });

      return response;
    } catch (err: any) {
      mediaError.value = err.data?.message || "Failed to update media name.";
      throw err;
    } finally {
      isUpdating.value = false;
    }
  };

  /**
   * Delete a media entry (and its physical file if local)
   */
  const deleteMedia = async (id: number): Promise<{ message: string } | null> => {
    isDeleting.value = true;
    mediaError.value = null;

    try {
      const response = await api.request<{ message: string }>(`/api/media/${id}`, {
        method: "DELETE",
      });

      return response;
    } catch (err: any) {
      mediaError.value = err.data?.message || "Failed to delete media.";
      throw err;
    } finally {
      isDeleting.value = false;
    }
  };

  return {
    isUploading,
    isUpdating,
    isDeleting,
    mediaError,
    uploadMedia,
    updateMedia,
    deleteMedia,
  };
};