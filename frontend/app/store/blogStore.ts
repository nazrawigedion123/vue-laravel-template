// frontend/app/store/blogStore.ts
import { defineStore } from "#imports";
import type{Blog ,BlogsResponse} from "~/types/blog"



export const useBlogStore=defineStore("blog",()=>{
    const api=useApi();


    const blogs=ref<Blog[]>([])





    const getBlogs=async()=>{

        try{
            
            const res=await api.request<BlogsResponse>('/blogs')
            blogs.value=res.data
            return blogs.value

        }catch(error){

            return null

        }

    }

    return{
        blogs,
        getBlogs
    }
    
})