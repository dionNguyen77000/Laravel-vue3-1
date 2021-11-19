<template>
  <!-- <div class="leading-normal tracking-normal" id="main-body"> -->
    <div class="flex flex-col md:flex-row">

      <div :class="sideBarOpen ? 'flex-none' : 'hidden'" >
      <Sidebar_Delivery />
      </div>
      <!-- <div class="w-full bg-gray-100 pl-0 lg:pl-64 min-h-screen" :class="sideBarOpen ? 'overlay' : ''" id="main-content"> -->


        <!-- <Navbar /> -->

      <div :class="sideBarOpen ? 'flex-auto' : 'w-full'">
        <!-- <Home /> -->
        <router-view> </router-view>
      </div>


      <!-- </div> -->
    </div>
  <!-- </div> -->

</template>



<script>
import auth from '../router/middleware/auth'
// import redirectIfNotFirstLevelUser from '../router/middleware/redirectIfNotFirstLevelUser'
// import redirectIfNotSecondLevelUser from '../router/middleware/redirectIfNotSecondLevelUser'
    // import redirectIfNotCustomer from '../router/middleware/redirectIfNotCustomer'

import { mapState } from 'vuex'

import Home from './Home.vue'

export default {
  middleware: [
    auth, 
    // redirectIfNotFirstLevelUser,
    // redirectIfNotSecondLevelUser
  ],
 computed: {
    ...mapState(['sideBarOpen'])
  },

  components: {
    Home
  },

  
    
}

</script>