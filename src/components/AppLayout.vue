<template>
    <div class="w-full flex flex-wrap pr-4 pl-2 py-4">
        <div class="flex flex-wrap w-full items-center justify-center">
            <span class="text-primary py-2 rounded-full text-2xl font-medium flex flex-wrap items-center justify-center">
                Robust User Search
                <svg class="h-8 w-8 inline-block ml-3" viewBox="0 0 110 110"><path fill="url(#paint0_linear)" fill-rule="evenodd" d="M53.4 4.9c1-.4 2.2-.4 3.2 0l36.7 13.7c1.8.7 3 2.4 3 4.3V55c0 15.8-10.5 28.4-20 36.7a104 104 0 01-19.1 13.2H57l-2.1-4-2 4h-.1a27.3 27.3 0 01-1.7-.9 97.3 97.3 0 01-17.6-12.3C24.2 83.4 13.7 70.8 13.7 55V23c0-2 1.2-3.7 3-4.4L53.4 5zm1.6 96l-2 4c1.2.7 2.8.7 4 0l-2-4zm0-5.3a89.9 89.9 0 0015.3-10.8c9-7.8 16.8-18.1 16.8-29.8V26L55 14 23 26v29c0 11.7 7.8 22 16.7 29.8A94.8 94.8 0 0055 95.6z" clip-rule="evenodd"/><path fill="url(#paint1_linear)" fill-rule="evenodd" d="M58 31.3c1 .4 1.4 1.3 1.3 2.3l-1.8 14.9h17a2.2 2.2 0 011.7 3.6L54.5 78a2.2 2.2 0 01-3.8-1.7l1.8-14.9h-17a2.2 2.2 0 01-1.7-3.6l21.7-26c.6-.7 1.6-1 2.5-.6zM40.1 57.2H55a2.2 2.2 0 012.1 2.4l-1.2 10 14-16.8H55a2.2 2.2 0 01-2.1-2.4l1.2-10-14 16.8z" clip-rule="evenodd"/><defs><linearGradient id="paint0_linear" x1="55" x2="55" y1="4.6" y2="105.4" gradientUnits="userSpaceOnUse"><stop offset=".4" stop-color="#0A192F"/><stop offset="1" stop-color="#3EFFF3"/></linearGradient><linearGradient id="paint1_linear" x1="55" x2="55" y1="31.2" y2="78.8" gradientUnits="userSpaceOnUse"><stop offset=".4" stop-color="#0A192F"/><stop offset="1" stop-color="#3EFFF3"/></linearGradient></defs></svg>
            </span>
        </div>
        <SearchFilter/>
        <div class="flex flex-wrap items-center justify-center absolute top-0 right-0 p-1"> 
            <a href="https://smitpatelx.com" target="_blank" class="text-sm font-medium text-teal-100 hover:text-teal-300 focus:text-teal-300 focus:outline-none focus:shadow-outline py-3 px-4 rounded-l-lg rounded-br-lg bg-primary">Smit Patel</a>
        </div>
    </div>
</template>
<script>
import SearchFilter from './SearchFilter'
export default {
    data(){
        return{

        }
    },
    components:{
        SearchFilter
    }
}
</script>