import { defineStore } from "#imports";

export const useLanguageStore=defineStore("languagePreference",()=>{
 
    const currentLanguagePreference =useCookie<string>("languagePreference",{
        default:()=>"en",
        path:"/"
    })

    const setLanguagePreference=(languageCode :string, page: string)=>{
        if (currentLanguagePreference.value===languageCode)return;
        currentLanguagePreference.value=languageCode

        // fetch page content with page and language code

    }

    return {
        currentLanguagePreference,
        setLanguagePreference
    }

})