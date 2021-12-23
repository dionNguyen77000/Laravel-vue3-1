<template>
<div  @click.self="closeModal" class=" overflow-y-auto backdrop flex  justify-center">
       {{orderDetail}}
    <div class="xl:w-1/4 lg:w-2/6 md:w-1/2 sm:w-full xs:w-full rounded-lg shadow-lg bg-white my-3" id="Order_combine" > 
             
        <div class="m-2 sm:w-full flex justify-left">
        <button @click="closeModal" class="rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:text-sm">Close</button>
        </div>           
        <div class="mb-2 flex justify-center border-b border-gray-100">  
            <span class="font-bold text-gray-700 text-lg">Orders :  {{selectedOrderNumbers}}</span>
        </div>
        <div class="relative flex flex-col  p-2 rounded-2xl">
                    <!-- {{order}} -->                  
                    <div class="absolute inset-0 w-full h-full bg-blue-50 rounded-2xl"></div>
                    <div class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-2xl"></div>       
                
                    <div class="relative mt-1 border-2 border-indigo-700 bg-white flex flex-col text-sm">
                        <!-- DeepFried + Rice Section -->
                        <div class="border-b-2 border-indigo-700" >
                            <template v-for="item_familyGroups,index in combinedOrderDetailsOfTwoOrders.slice(1,3)" :key="index" >  
                            <!-- Loop throug each family Group (chicken, beef, etc...) -->
                            <template v-for="item_familyGroup in item_familyGroups"  :key="item_familyGroup">
                                <div class="border border-gray-200  p-1 rounded-sm shadow-sm"
                                :class="{ 
                                        'bg-white': item_familyGroup.return_time ,
                                        'bg-gray-300' : item_familyGroup.is_make=='1',                                  
                                    }"
                                >
                                            <div class="flex justify-between  gap-1">
                                                <div class="text-lg font-bold" > 
                                                    {{item_familyGroup.quantity}}                   
                                                </div>
                                                <div class="text-gray-500 font-medium text-sm">
                                                    {{item_familyGroup.unit}} 
                                                </div>                                          
                                                <div>                                              
                                                    <p class="text-gray-700 font-bold tracking-wider">                                                 
                                                        {{item_familyGroup.menu_item_name}}
                                                    </p>
                                                    <p class="text-gray-500 text-sm font-medium">{{item_familyGroup.description}}</p>
                                                        <template v-if="item_familyGroup.condiments.length > 0">
                                                        <p v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                                            {{condiment.menu_item_name}}
                                                        </p>
                                                        </template>                                    
                                                </div>
                                                <a 
                                                    href="#"  @click.prevent = "completeTheItem(item_familyGroup)" 
                                                    class="h-6 w-12 mr-1 py-1 px-1 shadow-md rounded-full bg-green-500 text-white text-xs hover:bg-green-700 focus:outline-none"
                                                > 
                                                    {{ item_familyGroup.is_make ? 'UnMark' : 'Mark' }}

                                                </a>
                                            </div>
                                        </div>    
                                    </template>                           
                            </template>
                        </div>
                    <!-- StirFry Section -->
                        <div class="border-b-1 border-indigo-700" >               
                            <template v-for="item_familyGroups,index in combinedOrderDetailsOfTwoOrders.slice(3,12)" :key="index" >  
                                <!-- Loop throug each family Group (chicken, beef, etc...) -->
                                <template v-for="item_familyGroup in item_familyGroups"  :key="item_familyGroup">
                                <div class="border border-gray-200  p-1 rounded-sm shadow-sm" 
                                
                                    :class="{ 
                                        'bg-white': item_familyGroup.return_time ,
                                        'bg-gray-400 text-white hover:bg-gray-500' : item_familyGroup.is_make==1,                                   
                                    }"
                                    >                                
                                            <div class="flex justify-between  gap-1">
                                                <div class="text-lg font-bold" > {{item_familyGroup.quantity}}                   
                                                </div>
                                                <div class="text-gray-500 font-medium text-sm">
                                                    {{item_familyGroup.unit}} 
                                                </div>
                                                <div>
                                                    <p class="font-bold tracking-wider">{{item_familyGroup.menu_item_name}}</p>
                                                    <p class="text-gray-500 text-sm font-medium">{{item_familyGroup.description}}</p>
                                                        <template v-if="item_familyGroup.condiments.length > 0">
                                                        <p v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                                            {{condiment.menu_item_name}}
                                                        </p>
                                                        </template>                                    
                                                </div>
                                                <a href="#"  @click.prevent = "completeTheItem(item_familyGroup)" class="h-6 w-12 mr-1 py-1 px-1 shadow-md rounded-full bg-green-500 text-white text-xs hover:bg-green-700 focus:outline-none"> 
                                                    {{ item_familyGroup.is_make ? 'UnMark' : 'Mark' }}
                                                </a>
                                            </div>
                                        </div>    
                                    </template>                           
                            </template>
                        </div>
                        <!-- Family Pack, Lunch Special, Miscellaneous, Drinks, Gift Section -->
                        <div class="border border-indigo-700" >               
                            <template v-for="item_familyGroups,index in combinedOrderDetailsOfTwoOrders.slice(12)" :key="index" >  
                                <!-- Loop throug each family Group (chicken, beef, etc...) -->
                                <template v-for="item_familyGroup in item_familyGroups"  :key="item_familyGroup">
                                <div class="border border-gray-200  p-1 rounded-sm shadow-sm" 
                                
                                :class="{ 
                                        'bg-white': item_familyGroup.return_time ,
                                        'bg-gray-400 text-white hover:bg-gray-500' : item_familyGroup.is_make==1,                                 
                                    }"
                                    >                                
                                            <div class="flex justify-between  gap-1">
                                                <div class="text-lg font-bold" > {{item_familyGroup.quantity}}                   
                                                </div>
                                                <div class="text-gray-500 font-medium text-sm">
                                                    {{item_familyGroup.unit}} 
                                                </div>
                                                <div>
                                                    <p class="text-gray-700 font-bold tracking-wider">{{item_familyGroup.menu_item_name}}</p>
                                                    <p class="text-gray-500 text-sm font-medium">{{item_familyGroup.description}}</p>
                                                        <template v-if="item_familyGroup.condiments.length > 0">
                                                        <p v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                                            {{condiment.menu_item_name}}
                                                        </p>
                                                        </template>                                    
                                                </div>
                                                <a href="#"  @click.prevent = "completeTheItem(item_familyGroup)" class="h-6 w-12 mr-1 py-1 px-1 shadow-md rounded-full bg-green-500 text-white text-xs hover:bg-green-700 focus:outline-none"> 
                                                    {{ item_familyGroup.is_make ? 'UnMark' : 'Mark' }}
                                                </a>
                                            </div>
                                        </div>    
                                    </template>                           
                            </template>
                        </div>
                    </div>                
        </div>

    </div>
 
 </div>

</template>



<script>
import {mapGetters } from 'vuex'
export default {
    props: ['combinedOrderDetailsOfTwoOrders', 'selectedOrderNumbers','orderDetail'],
   data() {
            return {
             
            }
        },
       
 computed: {
    ...mapGetters({
        getAuth: 'auth/getAuth',
        firstLevelUsers: 'firstLevelUsers' ,
        secondLevelUsers: 'secondLevelUsers' ,
        thirdLevelUsers: 'thirdLevelUsers' ,
        fourthLevelUsers: 'fourthLevelUsers' ,

    }),

     getPermissionNames(){
        const permissionNameArray = _.map(this.getAuth.user.permissions, 'name');
        return permissionNameArray;
    },
    filteredRecords () {
              
    },
        
    getRoleNames(){
        const rolNameArray = [];
        const allRoleNames = this.getAuth.user.roles;
        allRoleNames.forEach(element => {
            rolNameArray.push(element.name)
        });
        
        return rolNameArray;
    },
   
    isFirstLevelUser() {
        let firstLevelUser = false;
        this.firstLevelUsers.forEach(element => {
            if(this.getRoleNames.includes(element)){
                firstLevelUser = true;
            }
        });
        return firstLevelUser;
    }
  },

  methods: {
      
        closeModal(){
            this.$emit('close')
        },

         completeTheItem(order_detail){
            if(order_detail.is_make == 0 || order_detail.is_make==null){

                order_detail.is_make = 1
            } else {
                order_detail.is_make = 0
            }
           
        },

        
        // refreshRecords(){
        //     this.$emit('refreshRecords')
        // },


     

     

    },
   
  
mounted() {
    console.table(this.combinedOrderDetailsOfTwoOrders)
},
    
}

</script>

<style  lang="scss">
    


.modal {
    margin: 30px auto;
    border-radius: 10px;
    background-color: black;
}

.backdrop {
    top:0;
    left:0;
    position: fixed;
    background: rgba(0,0,0,0.5);
    width:100%;
    height:100%;
    z-index: 9999; 
}

</style>
