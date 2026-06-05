// frontend/app/store/blogStore.ts
import { defineStore } from "#imports";
import type{Blog ,BlogsResponse} from "~/types/blog"
import { useAuthStore } from "./authStore";



export const useBlogStore=defineStore("blog",()=>{
    const api=useApi();
    const auth=useAuthStore();
    const{token}=auth;


    const blogs=ref<Blog[]>([])





    const getBlogs=async()=>{

        try{
            
            const res=await api.request<BlogsResponse>('/blogs')
            blogs.value=res.data
            return blogs.value

        }catch(error){

            return null

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
        } catch (error) {
            // 4. If the API fails, roll back to the previous state
            console.error("Failed to delete blog, rolling back:", error);
            blogs.value = previousBlogs;
            
            return false;
        }
    };

    return{
        blogs,
        getBlogs,
        removeBlog,
    }
    
})