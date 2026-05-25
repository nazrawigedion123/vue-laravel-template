
import { defineStore } from "#imports"
import type{ PageName } from "~/types/page"

export const usePageStore=defineStore("page",()=>{
  const  currentPage=ref<PageName>('home')

  const setCurrentPage=(pageName :PageName)=>{
    currentPage.value=pageName
  }

  return{
    currentPage,
    setCurrentPage
  }
}) 