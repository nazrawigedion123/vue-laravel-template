// frontend/app/store/blogStore.ts
import { defineStore } from "#imports";
import type{ Blog ,
            BlogsResponse,
            BlogCreateRequest,
            BlogCreateResponse, 
            BlogEditRequest,
            BlogEditResponse,
            AddBlogSectionRequest,
            AddBlogSectionResponse,
            EditBlogSectionRequest,
            EditBlogSectionResponse} from "~/types/blog"


import { useAuthStore } from "./authStore";


export const useBlogStore=defineStore("blog",()=>{
    // api
    const api=useApi();
    //auth store 
    const auth=useAuthStore();
    const{token}=auth;

    // blogError
    const blogError = ref<string | null>(null);

    // loaders
    const isCreating = ref(false);
    const isEditing =ref(false);
    const isLoading=ref(false);


    // blog savers    
    const blogs=ref<Blog[]>([])





    /**
     * The function `getBlogs` asynchronously fetches blogs data from an API, handles errors, and
     * updates loading state accordingly.
     * @returns The `getBlogs` function is returning the `blogs.value` after fetching the blogs data
     * from the API. If an error occurs during the API request, it will return `null`.
     */
    const getBlogs=async()=>{
        isLoading.value=true;
        blogError.value=null;

        try{
            
            const res=await api.request<BlogsResponse>('/blogs')
            blogs.value=res.data
            return blogs.value

        }catch(error:any){
            blogError.value = error.data?.message || "Failed to fetch blogs.";
            return null

        }finally{

            isLoading.value=false
        }

    };



    /**
     * The function `getBlogById` retrieves a blog by its ID, optionally refreshing the data from the
     * server, and updates the local state with the fetched blog.
     * @param {number} id - The `id` parameter in the `getBlogById` function is a number that
     * represents the unique identifier of the blog you want to retrieve.
     * @param {boolean} [forceRefresh=false] - The `forceRefresh` parameter in the `getBlogById`
     * function is a boolean flag that determines whether to fetch the blog data from the server even
     * if it exists in the local state. If `forceRefresh` is set to `true`, the function will always
     * make a request to the server to
     * @returns The `getBlogById` function returns a Promise that resolves to either a `Blog` object if
     * the blog is found or `null` if the blog is not found or an error occurs during the process.
     */
    const getBlogById = async (id: number, forceRefresh: boolean = false): Promise<Blog | null> => {
    blogError.value = null;
    
    // Check if blog exists in local state and not forcing refresh
    if (!forceRefresh) {
        const existingBlog = blogs.value.find(blog => blog.id === id);
        if (existingBlog) {
            return existingBlog;
        }
    }
    
    try {
        const res = await api.request<Blog>(`/blogs/${id}`, {
            method: 'GET',
            token: auth.token
        });
        
        if (res && res.id) {
            // Update local state
            const index = blogs.value.findIndex(blog => blog.id === id);
            if (index !== -1) {
                blogs.value[index] = res;
            } else {
                blogs.value.push(res);
            }
            
            return res;
        }
        return null;
    } catch (error: any) {
        blogError.value = error.data?.message || `Failed to fetch blog with id ${id}.`;
        return null;
    }
    };




   
   /**
    * The `createBlog` function asynchronously creates a new blog post, updates the local state with
    * the newly created blog, and returns the ID of the new blog post or null in case of an error.
    * @param {BlogCreateRequest} blogCreateRequest - The `blogCreateRequest` parameter in the
    * `createBlog` function is of type `BlogCreateRequest`. It likely contains the data needed to
    * create a new blog post, such as the title, content, tags, and any other relevant information
    * required for creating a blog post. This data is sent
    * @returns The `createBlog` function returns a `Promise` that resolves to either a `number` (the ID
    * of the newly created blog) or `null`.
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


    

    /**
     * The function `editBlog` asynchronously updates a blog post with the provided request data and
     * handles errors accordingly.
     * @param {BlogEditRequest} blogEditRequest - The `blogEditRequest` parameter is an object that
     * contains the data needed to edit a blog post. It likely includes properties such as the title,
     * content, tags, and any other information that can be updated for a blog post.
     * @param {number} id - The `id` parameter in the `editBlog` function represents the unique
     * identifier of the blog post that you want to edit. This identifier is used to locate the
     * specific blog post within the array of blogs and to make the necessary updates to that
     * particular blog post.
     * @returns The `editBlog` function returns a Promise that resolves to either a `Blog` object if
     * the editing is successful, or `null` if there is an error during the editing process.
     */
    const editBlog = async (blogEditRequest: BlogEditRequest, id: number): Promise<Blog | null> => {
    isEditing.value = true;
    blogError.value = null;
    
    const index = blogs.value.findIndex(blog => blog.id === id);

    try {
        const res = await api.request<BlogEditResponse>(`/blogs/${id}`, {
            method: 'POST',
            token: auth.token,
            body: blogEditRequest,
        });
        
        if (res && res.blog && res.blog.id && index !== -1) {
            // Create a new object by spreading and ensure all properties
            const updatedBlog = Object.assign({}, blogs.value[index], {
                medias: res.blog.medias,
                translations: res.blog.translations
            }) as Blog;
            
            blogs.value[index] = updatedBlog;
            return updatedBlog;
        }
        
        return null;

    } catch (error: any) {
        blogError.value = error.data?.message || "Failed to edit blog post.";
        return null;
    } finally {
        isEditing.value = false;
    }
    };


   /**
    * The `removeBlog` function in TypeScript removes a blog optimistically from the UI, makes an API
    * call to delete it on the backend, and handles errors by rolling back to the previous state.
    * @param {number} id - The `id` parameter in the `removeBlog` function represents the unique
    * identifier of the blog that needs to be removed. This identifier is used to target the specific
    * blog for deletion both in the UI and when making the API call to the backend.
    * @returns The `removeBlog` function returns a boolean value - `true` if the deletion operation was
    * successful, and `false` if there was an error during the deletion process.
    */    
    const removeBlog = async (id: number) => {
        // 1. Save a backup of the current state in case the API fails
        const previousBlogs = [...blogs.value];
        isLoading.value=true;

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
        }finally{
            isLoading.value=false;
        }
    };



    /**
     * The function `createBlogSection` asynchronously adds a new section to a blog and updates the
     * blog with the new section if successful.
     * @param {number} blogID - The `blogID` parameter is a number that represents the unique
     * identifier of the blog to which the new blog section will be added.
     * @param {AddBlogSectionRequest} blogSection - The `blogSection` parameter in the
     * `createBlogSection` function represents the content of the new section that you want to add to a
     * blog. It is of type `AddBlogSectionRequest`, which likely contains details such as the title,
     * content, and any other relevant information for the new section
     * @returns The `createBlogSection` function returns a Promise that resolves to either a `Blog`
     * object if the blog section was successfully created and added to the blog, or `null` if there
     * was an error during the process.
     */
    const createBlogSection=async(blogID : number, blogSection : AddBlogSectionRequest):Promise<Blog|null>=> {
        isCreating.value = true;
        blogError.value = null;


         const index = blogs.value.findIndex(blog => blog.id === blogID);
         const currentBlog = blogs.value[index];
        if (!currentBlog) {
            blogError.value = "Blog not found.";
            isCreating.value = false;
            return null;
        }
        
        try {
            const res = await api.request<AddBlogSectionResponse>(`/blog/${blogID}/sections`, {
                method: 'POST',
                token: auth.token,
                body: blogSection
            });

          
           if (res && res.section && res.section.id) {
            // Create updated blog with new section
            const updatedBlog: Blog = {
                ...currentBlog,
                sections: [...currentBlog.sections, res.section]
            };
            
            blogs.value[index] = updatedBlog;
            return updatedBlog;
        }
        return null
        } catch (error: any) {
            blogError.value = error.data?.message || "Failed to create blog post.";
            return null;
        } finally {
            isCreating.value = false;
        }
    };




    /**
     * The function `editBlogSection` updates a specific section within a blog and returns the updated
     * blog if successful.
     * @param {number} blogID - The `blogID` parameter in the `editBlogSection` function refers to the
     * unique identifier of the blog that contains the section you want to edit. It is used to locate
     * the specific blog within the list of blogs.
     * @param {number} sectionID - The `sectionID` parameter in the `editBlogSection` function refers
     * to the unique identifier of the blog section that you want to edit within a specific blog. It is
     * used to locate the specific section within the blog's sections array and update its content
     * based on the provided `blogSection` data
     * @param {EditBlogSectionRequest} blogSection - The `blogSection` parameter in the
     * `editBlogSection` function represents the data that you want to update for a specific section of
     * a blog. It is of type `EditBlogSectionRequest`, which likely contains properties such as
     * `title`, `content`, or any other fields that you want to
     * @returns The `editBlogSection` function returns a `Promise` that resolves to either a `Blog`
     * object if the blog section was successfully edited and updated, or `null` if there was an error
     * during the process.
     */
    const editBlogSection = async (blogID: number, sectionID: number, blogSection: EditBlogSectionRequest): Promise<Blog | null> => {
    isEditing.value = true;
    blogError.value = null;

    const index = blogs.value.findIndex(blog => blog.id === blogID);
    const currentBlog = blogs.value[index];
    
    if (!currentBlog) {
        blogError.value = "Blog not found.";
        isEditing.value = false;
        return null;
    }
    
    const sectionIndex = currentBlog.sections.findIndex(section => section.id === sectionID);
    
    if (sectionIndex === -1) {
        blogError.value = "Section not found.";
        isEditing.value = false;
        return null;
    }
    
    try {
        const res = await api.request<EditBlogSectionResponse>(`/blog/${blogID}/sections/${sectionID}`, {
            method: 'PUT', 
            token: auth.token,
            body: blogSection
        });

        if (res && res.section && res.section.id) {
            // Replace the section at the found index with the updated one
            const updatedSections = [...currentBlog.sections];
            updatedSections[sectionIndex] = res.section;
            
            const updatedBlog: Blog = {
                ...currentBlog,
                sections: updatedSections
            };
            
            blogs.value[index] = updatedBlog;
            return updatedBlog;
        }
        return null;
    } catch (error: any) {
        blogError.value = error.data?.message || "Failed to edit blog section.";
        return null;
    } finally {
        isEditing.value = false;
    }
    };



    /**
     * The `removeBlogSection` function asynchronously removes a section from a blog, updating the UI
     * optimistically and handling API errors by rolling back to the previous state.
     * @param {number} blogID - The `blogID` parameter in the `removeBlogSection` function refers to
     * the unique identifier of the blog from which you want to remove a section. This ID is used to
     * locate the specific blog in the list of blogs and then find the section within that blog that
     * needs to be removed.
     * @param {number} sectionID - The `sectionID` parameter in the `removeBlogSection` function refers
     * to the unique identifier of the section within a specific blog that you want to remove. This ID
     * is used to locate the specific section within the blog's sections array and perform the deletion
     * operation either on the frontend UI optimistically or
     * @returns The `removeBlogSection` function returns a `Promise<boolean>`. The function returns
     * `true` if the section was successfully deleted, and `false` if there was an error during the
     * deletion process.
     */
    const removeBlogSection = async (blogID: number, sectionID: number): Promise<boolean> => {
    // 1. Save backup of the current state
    const previousBlogs = [...blogs.value];
    isLoading.value = true;
    blogError.value = null;

    // 2. Find the blog and section index
    const blogIndex = blogs.value.findIndex(blog => blog.id === blogID);
    const currentBlog = blogs.value[blogIndex];
    
    if (!currentBlog) {
        blogError.value = "Blog not found.";
        isLoading.value = false;
        return false;
    }
    
    const sectionIndex = currentBlog.sections.findIndex(section => section.id === sectionID);
    
    if (sectionIndex === -1) {
        blogError.value = "Section not found.";
        isLoading.value = false;
        return false;
    }

    // 3. Optimistically update the UI by removing the section immediately
    const updatedSections = currentBlog.sections.filter(section => section.id !== sectionID);
    const updatedBlog: Blog = {
        ...currentBlog,
        sections: updatedSections
    };
    
    blogs.value[blogIndex] = updatedBlog;

    try {
        // 4. Make the actual API call to delete on the backend
        await api.request(`/blog/${blogID}/sections/${sectionID}`, {
            method: 'DELETE',
            token: auth.token,
        });
        
        return true;
    } catch (error: any) {
        // 5. If API fails, roll back to the previous state
        blogError.value = error.data?.message || `Failed to delete section ${sectionID}.`;
        blogs.value = previousBlogs;
        
        return false;
    } finally {
        isLoading.value = false;
    }
    };

    return{
        // saved blog
        blogs,
        // fetchBlogs
        getBlogs,
        getBlogById,
        // actions on blog
        removeBlog,
        createBlog,
        editBlog,
        // actions on blog section
        createBlogSection,
        editBlogSection,
        removeBlogSection,

        // blog error
        blogError,
        // loaders
        isCreating,
        isEditing,
        isLoading,
    }
    
})