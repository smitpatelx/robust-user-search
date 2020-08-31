<template>
    <div class="w-full flex flex-wrap">
        <div class="w-full mt-2 flex">
            <input @keydown.esc="search_text=''" class="text-base bg-blue-500 placeholder-text-gray-200" type="text" placeholder="Search User" v-model="search_text">
            <button class="bg-primary hover:bg-gray-700 transition-colors duration-500 text-white px-2 ml-2 rounded flex flex-wrap justify-center items-center focus:outline-none focus:shadow-outline">
                <svg class="w-4 h-4 fill-current inline-block text-white mr-2" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M13.4 1c.4.3.6.7.6 1.1L13 9H21a1 1 0 01.8 1.6l-10 12A1 1 0 0110 22L11 15H3a1 1 0 01-.8-1.6l10-12a1 1 0 011.2-.3zM5.1 13H12a1 1 0 011 1.1l-.6 4.6L19 11H12a1 1 0 01-1-1.1l.6-4.6L5 13z" clip-rule="evenodd"/></svg> 
                <span class="text-base">Search</span>
            </button>
            <select @change="getNewRoles" v-model="currentRole" name="role" class="w-40 ml-10">
                <option value="" default >All Roles</option>
                <option v-for="(rol,i) in roles" :key="i" :value="rol.name">{{rol.name || capitalize}}</option>
            </select>
        </div>
        <div class="w-full mt-4 flex flex-wrap flex-row bg-white rounded shadow-xl">
            <div class="text-base w-full text py-3 flex items-center justify-center text-blue-800 font-medium border-b">
                <span class="pl-4 pr-2 py-1 text-left border-r border-gray-400 flex-1">Username</span>
                <span class="pl-4 pr-2 py-1 text-left border-r border-gray-400 w-48">Name</span>
                <span class="pl-4 pr-2 py-1 text-left border-r border-gray-400 flex-1">Email</span>
                <span class="pl-4 pr-2 py-1 text-left border-r border-gray-400 w-40">Company</span>
                <span class="pl-4 pr-2 py-1 text-left border-r border-gray-400 flex-1">Role</span>
            </div>
            <div class="w-full items-start overflow-y-scroll custom_scroll" style="height: 32rem;">
                <a :href="'http://wp02.com/wp-admin/user-edit.php?user_id='+user.id+'&wp_http_referer=%2Fwp-admin%2Fusers.php'" target="_blank" v-for="(user,i) in search_arr" :key="i" class="text-base w-full text py-2 flex items-center justify-center text-gray-700 text-sm bg-gray-100 hover:bg-gray-200 focus:outline-none focus:bg-gray-200">
                    <span class="pl-4 pr-2 py-1 text-left border-r border-blue-200 flex-1 underline" :class="user.username ? '' : 'text-red-500'">{{user.username}}</span>
                    <span class="pl-4 pr-2 py-1 text-left border-r border-blue-200 capitalize w-48" 
                        :class="user.first_name && user.last_name ? '' : 'text-red-500'"
                        :title="displayTitleForName(user.first_name, user.last_name)" >
                        {{user.first_name}} {{user.last_name}}
                    </span>
                    <span class="pl-4 pr-2 py-1 text-left border-r border-blue-200 flex-1" :class="user.email ? '' : 'text-red-500'">{{user.email}}</span>
                    <span class="pl-4 pr-2 py-1 text-left border-r border-blue-200 w-40  capitalize" :class="user.billing_company ? '' : 'text-red-500'">{{user.billing_company || 'Empty !!'}}</span>
                    <span class="pl-4 pr-2 py-1 text-left border-r border-blue-200 flex-1 flex flex-wrap capitalize">
                        <span v-for="(rl,x) in user.roles" :key='x' class="rounded-full bg-gray-600 text-white py-1 px-3 text-sm ml-2 my-1"> {{rl}}</span>
                    </span>
                </a>
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
            // console.log(this.users[1])
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
        }
    },
    watch:{
        search_text(val){
            let pattern=new RegExp(val.trim().toLowerCase(),"g")
            let search_arr = this.users.filter(function(arr,index){
                if(arr.username.toLowerCase().match(pattern) || 
                    arr.first_name.toLowerCase().match(pattern) ||
                    arr.last_name.toLowerCase().match(pattern) ||
                    arr.billing_company.toLowerCase().match(pattern) ||
                    arr.email.toLowerCase().match(pattern)
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
</style>