<template>
<!-- {{getAuth}} -->
  <div id="header" class="w-full flex justify-center min-w-screen  p-4 bg-yellow-600 lg:sticky top-0 z-40 text-center">
    <div id="logo">
      <img :src="'/img/logo_backend.png'" alt="Golden Lor Yarrabilba"
      class="text-center sm:hidden h-12 mx-auto pb-2"
      >
    </div>
    

    <nav class="w-full z-40 flex justify-between flex-wrap">
        <!-- {{getAuth}} -->
        <ul class="flex items-center">
          <div id="logo">
            <img :src="'/img/logo_backend.png'" alt="Golden Lor Yarrabilba"
            class="hidden sm:block  text-center h-14 ">
          </div>
            <router-link class="mr-4" to="/"> Home </router-link>
            <router-link class="mr-4" :to="{name:'Dashboard'}">Dashboard</router-link>
            <router-link class="mr-4" :to="{name:'Reminder'}">Reminder</router-link>
        </ul>
        <ul class="flex items-center">
        <!-- @if (auth()->user()) -->
        <template v-if="!getAuth.loggedIn">
          <!-- <router-link class="mr-4" :to="{name:'Register'}">Register</router-link> -->
          <router-link class="mr-4" :to="{name:'Login'}">Login</router-link>
        </template>
        <template v-else>
          <li class="mr-4">
          Hi  {{getAuth.user.name}}
          <!-- <img src="https://a7sas.net/wp-content/uploads/2019/07/4060.jpeg" class="w-12 h-12 rounded-full shadow-lg" @click="dropDownOpen = !dropDownOpen"> -->
        </li>
        <li>
          <a class="mr-4 cursor-pointer" @click.prevent="logOut">Logout</a>
            <!-- <router-link class="mr-4" :to="{name:'Logout'}">Logout</router-link> -->
            
        </li>
        </template> 
        </ul>
       
        <!-- @endif -->
</nav>
  </div>
  
</template>


<script>
import { mapState } from 'vuex'
import {mapGetters, mapActions} from 'vuex'

export default {
    name: 'Navbar',
    
    data() {
    },
    computed: {
         ...mapGetters({
           getAuth: 'auth/getAuth'
         }),
    },
    methods: {
       ...mapActions({
         logOutAction: 'auth/logOut'
       }),

       logOut () {
         this.logOutAction().then(()=>{
           this.$router.replace({
             name: 'Login'
           })
         })
       }
    },
}
</script>
