<template>
    <div class="w-full flex flex-wrap">
        <div class="w-full mt-2 flex">
            <input @keydown.esc="search_text=''" class="text-base bg-blue-500 placeholder-text-gray-200" type="text" placeholder="Search User" v-model="search_text" title="Press Esc to clear !">
            <button @click.prevent="searchUser" class="bg-primary hover:bg-gray-700 transition-colors duration-500 text-white px-2 ml-2 rounded flex flex-wrap justify-center items-center focus:outline-none focus:shadow-outline">
                <svg class="w-4 h-4 fill-current inline-block text-white mr-2" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M13.4 1c.4.3.6.7.6 1.1L13 9H21a1 1 0 01.8 1.6l-10 12A1 1 0 0110 22L11 15H3a1 1 0 01-.8-1.6l10-12a1 1 0 011.2-.3zM5.1 13H12a1 1 0 011 1.1l-.6 4.6L19 11H12a1 1 0 01-1-1.1l.6-4.6L5 13z" clip-rule="evenodd"/></svg> 
                <span class="text-base">Search</span>
            </button>
            <select @change="getNewRoles" v-model="currentRole" name="role" class="w-40 ml-10">
                <option value="" default >All Roles</option>
                <option v-for="(rol,i) in roles" :key="i" :value="slug(rol.name)">{{rol.name || capitalize}}</option>
            </select>
        </div>
        <div class="w-full mt-4 flex flex-wrap flex-row">
            <div class="text-base w-full rounded shadow text py-3 grid grid-cols-5 items-center justify-center bg-primary text-blue-100 font-medium border-b">
                <span class="pl-4 pr-2 py-1 text-left border-r border-teal-400 overflow-x-hidden">Username</span>
                <span class="pl-4 pr-2 py-1 text-left border-r border-teal-400">Name</span>
                <span class="pl-4 pr-2 py-1 text-left border-r border-teal-400">Email</span>
                <span class="pl-4 pr-2 py-1 text-left border-r border-teal-400">Company</span>
                <span class="pl-4 pr-2 py-1 text-left">Role</span>
            </div>
            <div class="w-full items-start overflow-y-scroll custom_scroll" style="height: 32rem;">
                <div v-for="(user,i) in search_arr" :key="i" 
                    class="text-base w-full my-2 rounded-lg shadow-lg py-2 grid grid-cols-5 items-center justify-center text-gray-700 bg-gray-100 border border-gray-300 hover:bg-teal-100 focus:outline-none focus:bg-teal-100">
                    <span class="truncate pl-4 pr-2 py-1 text-left border-r border-gray-400 select-all overflow-x-hidden underline" :class="user.username ? '' : 'text-red-500'">{{user.username}}</span>
                    <span class="truncate pl-4 pr-2 py-1 text-left border-r border-gray-400 capitalize " 
                        :class="user.first_name && user.last_name ? '' : 'text-red-500'"
                        :title="displayTitleForName(user.first_name, user.last_name)" >
                        {{user.first_name}} {{user.last_name}}
                    </span>
                    <span class="truncate pl-4 pr-2 py-1 text-left border-r border-gray-400 select-all" :class="user.email ? '' : 'text-red-500'">{{user.email}}</span>
                    <span class="truncate pl-4 pr-2 py-1 text-left border-r border-gray-400 select-all capitalize" :class="user.billing_company ? '' : 'text-red-500'">{{user.billing_company || 'Empty !!'}}</span>
                    <span class="truncate pl-4 pr-2 py-1 text-left flex flex-wrap flex-row justify-between items-center capitalize select-none">
                        <div class="flex flex-wrap w-4/5">
                            <span v-for="(rl,x) in user.roles" :key='x' class="rounded-full bg-gray-600 text-white py-1 px-3 text-sm ml-2 my-1"> {{rl}}</span>
                        </div>
                        <a :href="'/wp-admin/user-edit.php?user_id='+user.id+'&wp_http_referer=%2Fwp-admin%2Fusers.php'"  target="_blank" class="w-1/5 text-teal-400 hover:text-teal-600">
                            <svg class="w-5 h-5 fill-current inline-block mr-2" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M5 7a1 1 0 00-1 1v11a1 1 0 001 1h11a1 1 0 001-1v-6a1 1 0 112 0v6a3 3 0 01-3 3H5a3 3 0 01-3-3V8a3 3 0 013-3h6a1 1 0 110 2H5zM14 3c0-.6.4-1 1-1h6c.6 0 1 .4 1 1v6a1 1 0 11-2 0V4h-5a1 1 0 01-1-1z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M21.7 2.3c.4.4.4 1 0 1.4l-11 11a1 1 0 01-1.4-1.4l11-11a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
                        </a>
                    </span>
                </div>
                <div v-if="search_arr.length==0" class="text-base w-full text py-3 flex items-center justify-center text-red-500 bg-red-100 font-medium">
                    <span class="pl-4 pr-2 py-1 text-center flex-1">No Data Found. Try different search.</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
export default {
    data(){
        return{
            search_text:'',
            users:[],
            currentRole: '',
            search_arr:[],
            roles:[]
        }
    },
    methods:{
        async getAllUsers(){
            let res = await axios({
                method: 'get',
                url: `/wp-json/rsu/v1/all`,
                headers: {
                    'X-WP-Nonce':rusN.nonce
                }
            });
            this.users = res.data;
            this.search_arr = res.data;
        },
        displayTitleForName(fn, ln){
            if(fn.trim().length+ln.trim().length == 0){
                return "First & Last name missing!"
            } else if(ln.trim().length == 0) {
                return "Last name missing!"
            } else if(fn.trim().length == 0){
                return "First name missing!"
            }
        },
        async getNewRoles(){
            let res = await axios({
                method: 'get',
                url: `/wp-json/rsu/v1/all`,
                params:{
                    role: this.currentRole
                },
                headers: {
                    'X-WP-Nonce':rusN.nonce
                }
            });
            this.users = res.data;
            this.search_arr = res.data;
        },
        async getAllRoles(){
            let res = await axios({
                method: 'get',
                url: `/wp-json/rsu/v1/roles`,
                headers: {
                    'X-WP-Nonce':rusN.nonce
                }
            });
            this.roles = res.data;
            console.log(res.data)
        },
        slug(str){
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();
        
            // remove accents, swap ñ for n, etc
            var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to   = "aaaaeeeeiiiioooouuuunc------";
            for (var i=0, l=from.length ; i<l ; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '_') // collapse whitespace and replace by -
                .replace(/-+/g, '_'); // collapse dashes

            return str;
        },
        async searchUser(){
            let pattern = new RegExp(val.toString().trim().toLowerCase(),"g")
            let search_arr = await this.users.filter(function(arr,index){
                if(arr.username.toString().trim().toLowerCase().match(pattern) || 
                    arr.first_name.toString().trim().toLowerCase().match(pattern) ||
                    arr.last_name.toString().trim().toLowerCase().match(pattern) ||
                    arr.billing_company.toString().trim().toLowerCase().match(pattern) ||
                    arr.email.toString().trim().toLowerCase().match(pattern)
                    ){
                    return true;
                }else{
                    return false;
                }   
            });
            this.search_arr = search_arr;
        }
    },
    watch:{
        search_text(val){
            let pattern=new RegExp(val.toString().trim().toLowerCase(),"g")
            let search_arr = this.users.filter(function(arr,index){
                if(arr.username.toString().trim().toLowerCase().match(pattern) || 
                    arr.first_name.toString().trim().toLowerCase().match(pattern) ||
                    arr.last_name.toString().trim().toLowerCase().match(pattern) ||
                    arr.billing_company.toString().trim().toLowerCase().match(pattern) ||
                    arr.email.toString().trim().toLowerCase().match(pattern)
                    ){
                    return true;
                }else{
                    return false;
                }   
            });
            this.search_arr = search_arr;
        }
    },
    mounted(){
        this.getAllUsers()
        this.getAllRoles()
    },
    filters:{
        capitalize(val){
            return val.charAt(0).toUpperCase() + val.slice(1)
        }
    }
}
</script>
<style lang="scss">
    .custom_scroll{
        &::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            border-radius: 0px;
            background-color: rgb(42, 42, 42);
        }

        &::-webkit-scrollbar
        {
            width: 12px;
            background-color: #F5F5F5;
            display: none;
        }

        &::-webkit-scrollbar-thumb
        {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #31bbce;
        }
    }

    #wpbody-content{
        padding-bottom:0px !important;
    }
</style>