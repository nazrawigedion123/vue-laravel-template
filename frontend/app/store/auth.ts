import type { AuthResponse,AuthUser } from '~/types/api';
import { generateCodeVerifier, generateCodeChallenge } from '~/utils/pkce';



export const useAuthStore =defineStore('auth',()=>{
    const api=useApi()

    //State
    const token=useCookie<string|null>('auth_token',{
        sameSite:'lax',
        secure:false,
        default:()=>null,
    })

    //Standard refs instead of useState
    const user=ref<AuthUser| null>(null)
    const loading=ref(false)

    const isAuthenticated=computed(()=>Boolean(token.value))
    const isSuperuser=computed(()=>Boolean(user.value?.is_superuser))
    const isStaff=computed(()=>Boolean(user.value?.is_staff))
    const hasDashboardAccess=computed(()=>isSuperuser.value || isStaff.value)
    const canCreateBlog=computed(()=>Boolean(user.value?.is_superuser||user.value?.role?.can_create_blog))

    // Actions (Methods)
    const loadUser=async()=>{
        if (!token.value){
            user.value=null
            return null
        }

        try {
            user.value=await api.request<AuthUser>('/auth/me',{token:token.value})
            return user.value
        }catch(error){
            // clear invalid token
            token.value=null
            user.value=null
            return null
        }
    }
    

    const login =async (email :string , password :string)=>{
        loading.value=true
        try {
            const response =await api.request<AuthResponse>('/auth/login',{
                method:'POST',
                body:{email,password}
            })
            token.value=response.access_token
            await loadUser()
        }finally{
            loading.value=false
        }
    }

    const register=async (email:string, password:string)=>{
        loading.value=true
        try{
            const response =await api.request<AuthResponse>('/auth/register',{
              method:'POST',
              body:{email,password}  
            })
            token.value=response.access_token
            await loadUser()
        }finally{
            loading.value=false
        }
    }

    const logout =async ()=>{
        if (token.value){
            await api.request('/auth/logout',{method:  'POST',token:token.value}).catch(()=>null)
        }
        token.value=null
        user.value=null
    }

    const loginWithGoogle = async () => {
        const verifier = generateCodeVerifier()
        const challenge = await generateCodeChallenge(verifier)
        
        // Store verifier in cookie for later
        const verifierCookie = useCookie('google_code_verifier', { maxAge: 600 })
        verifierCookie.value = verifier
        

        const config = useRuntimeConfig()
        const clientId = config.public.googleClientID
        const redirectUri =config.public.googleCallBack
        const scope = 'openid profile email'
        
        const googleUrl = new URL('https://accounts.google.com/o/oauth2/v2/auth')
        googleUrl.searchParams.append('client_id', clientId)
        googleUrl.searchParams.append('redirect_uri', redirectUri)
        googleUrl.searchParams.append('response_type', 'code')
        googleUrl.searchParams.append('scope', scope)
        googleUrl.searchParams.append('code_challenge', challenge)
        googleUrl.searchParams.append('code_challenge_method', 'S256')
        
        window.location.href = googleUrl.toString()
    }

    const handleGoogleCallback = async (code: string) => {
        const verifierCookie = useCookie<string | null>('google_code_verifier')
        const verifier = verifierCookie.value

        if (!verifier) throw new Error('Missing code verifier')

        loading.value = true
        try {
            const response = await api.request<AuthResponse>('/auth/google/callback', {
                method: 'POST',
                body: { code, code_verifier: verifier }
            })
            token.value = response.access_token
            verifierCookie.value = null // Clear verifier
            await loadUser()
        } finally {
            loading.value = false
        }
    }
    return{
        token,
        user,
        loading,
        isAuthenticated,
        isSuperuser,
        isStaff,
        hasDashboardAccess,
        canCreateBlog,
        loadUser,
        login,
        register,
        logout,
        loginWithGoogle,
        handleGoogleCallback,
    }
})