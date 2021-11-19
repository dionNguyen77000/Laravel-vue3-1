<template>

    <!-- give the sidebar z-50 class so its higher than the navbar if you want to see the logo -->
    <!-- you will need to add a little "X" button next to the logo in order to close it though -->
    <div id="sideBar" class="pb-2 sticky top-24 z-30 left-0 bg-gray-900">
      <div class="h-20 border flex justify-center items-center mb-4 ">
       <!-- <p class="font-semibold text-xl text-red-400 pl-4">DASHBOARD</p> -->
       <div id="dashboard">
         <router-link :to="{name:'Delivery_Journey'}">
            <div class="font-semibold text-xl text-red-400 pl-2 hover:text-red-700 cursor-pointer">
                 Delivery
             </div>
          </router-link>
        </div>
      </div>  
       <div class="mb-4 px-4">
            <router-link :to="{name:'Delivery_Journey'}">
            <div class="mb-1 w-full flex justify-center  md:justify-start items-center text-blue-400 h-8 pl-4 hover:bg-gray-200 rounded-lg cursor-pointer">
                 Delivery Journey
             </div>
            </router-link>
        <template  v-if="getAuth.isFirstLevelUser|| getAuth.isSecondLevelUser" >
              <router-link :to="{name:'Delivery_Detail'}">
              <div class="mb-1 w-full flex justify-center md:justify-start items-center text-blue-400 h-8 pl-4 hover:bg-gray-200 rounded-lg cursor-pointer">
                Delivery Details
              </div>
             </router-link>

              <router-link :to="{name:'Delivery_Setting'}">
              <div class="mb-1 w-full flex justify-center md:justify-start items-center text-blue-400 h-8 pl-4 hover:bg-gray-200 rounded-lg cursor-pointer">
                Delivery Setting
              </div>
             </router-link>   

              <router-link :to="{name:'Delivery_Travel_Time'}">
              <div class="mb-1 w-full flex justify-center md:justify-start items-center text-blue-400 h-8 pl-4 hover:bg-gray-200 rounded-lg cursor-pointer">
                Travel Time
              </div>
             </router-link>    
        </template>
      </div>

    </div>
</template>

<script>
import { mapState } from 'vuex'
import {mapGetters, mapActions} from 'vuex'
export default {
  data() {
        return {
            
        }
    },
    name: 'Sidebar',
    computed: {
        ...mapState(['sideBarOpen']),
         ...mapGetters({
           getAuth: 'auth/getAuth'
         }),
          getPermissionNames(){
              const permissionNameArray = _.map(this.getAuth.user.permissions, 'name');
              return permissionNameArray;
          },

          // haveOrderManagementPermission(){
          //   let isCorrect = false;
          //   if (this.getPermissionNames.includes('Order_Management')) 
          //   {
          //       isCorrect = true;
          //       break;
          //   }
                
          //   return isCorrect;

          // },

    },
    methods: {
       
    }
}
</script>

<style>

#sideBar a  {
    padding-left: 3px;
    display: block;
    border-radius: 0.5rem;
    margin-bottom: 1px;
}

#sideBar .router-link-active{
    color:white;
    background-color:white;
}

#dashboard .router-link-active{
      background-color:black;
}

/* #sortbox:checked ~ #sortboxmenu {
      opacity: 1;
  } */


</style>

