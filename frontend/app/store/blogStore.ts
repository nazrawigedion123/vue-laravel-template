// frontend/app/store/blogStore.ts
import { defineStore } from "#imports";
import type{Blog ,BlogsResponse,BlogCreateRequest,BlogCreateResponse,Translation} from "~/types/blog"
import { useAuthStore } from "./authStore";



export const useBlogStore=defineStore("blog",()=>{
    const api=useApi();
    const auth=useAuthStore();
    const{token}=auth;
    const blogError = ref<string | null>(null);

    const isCreating = ref(false);


    const blogs=ref<Blog[]>([])





    const getBlogs=async()=>{

        try{
            
            const res=await api.request<BlogsResponse>('/blogs')
            blogs.value=res.data
            return blogs.value

        }catch(error:any){
            blogError.value = error.data?.message || "Failed to fetch blogs.";
            return null

        }

    };
    /**
     * Create a new blog post and update local state
     */
    const createBlog = async (blogCreateRequest: BlogCreateRequest): Promise<number | null> => {
        isCreating.value = true;
        blogError.value = null;
        
        try {
            const res = await api.request<BlogCreateResponse>(`/blogs`, {
                method: 'POST',
                token: auth.token,
                body: blogCreateRequest
            });

            // Assuming BlogCreateResponse provides the complete newly created blog object inside a key, 
            // or we build a temporary structural copy to update our local state instantly.
            if (res && res.blog_id) {
                // If your backend returns the fully formed blog structure inside the response:
                // e.g., if res.data contains the new Blog object:
                // blogs.value.unshift(res.data); 

                // Alternatively, if it only returns the blog_id, map your request into state:
               
                const newBlog: Blog = {
                    id: res.blog_id,
                    comment_count: 0,
                    reaction_count: 0,                     
                    author: auth.user?.email ?? 'NA',
                    published_at:  null,
                    sections: [],
                    comments: [],
                    media_ids: [],
                    translations:blogCreateRequest.translations,
                      
                };
                
                // Add the new blog to the top of the array
                blogs.value.unshift(newBlog);
                
                return res.blog_id;
            }
            return null;
        } catch (error: any) {
            blogError.value = error.data?.message || "Failed to create blog post.";
            return null;
        } finally {
            isCreating.value = false;
        }
    };
    const removeBlog = async (id: number) => {
        // 1. Save a backup of the current state in case the API fails
        const previousBlogs = [...blogs.value];

        // 2. Optimistically update the UI by removing the blog immediately
        blogs.value = blogs.value.filter(blog => blog.id !== id);

        try {
            // 3. Make the actual API call to delete it on the backend
            // Assuming your backend expects a DELETE request to /blogs/:id
            await api.request(`/blogs/${id}`, { method: 'DELETE',token:auth.token });
            
            // Return true or success status if needed
            return true;
        } catch (error:any) {
            // // 4. If the API fails, roll back to the previous state
            // console.error("Failed to delete blog, rolling back:", error);
            blogError.value = error.data?.message || "Failed to delete blog `{id}`.";
            blogs.value = previousBlogs;
            
            return false;
        }
    };

    return{
        blogs,
        getBlogs,
        removeBlog,
        createBlog,
        blogError,
        isCreating
    }
    
})