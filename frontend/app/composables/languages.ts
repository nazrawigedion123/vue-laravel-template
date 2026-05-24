// frontend/app/composables/languages.ts
import {type Language } from "~/types/language";
import { useApi } from "#imports";

export const useLanguages=(p0: { onResponse({ response }: { response: any; }): void; })=>{
    const api= useApi()


    return useAsyncData<Language[]>('languages',()=>
        api.request<Language[]>('/languages'))
}