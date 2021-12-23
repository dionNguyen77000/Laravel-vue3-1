<template>    
    <div class="relative flex flex-col justify-between p-2 rounded-2xl">
        <!-- {{order}} -->
        <div class="absolute inset-0 w-full h-full rounded-2xl"
            :class="order.remaining_time == 'Over' ? 'bg-red-400 ': 'bg-green-50'"

        ></div>
        <div class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-2xl"></div>
        <div class="relative" v-if="order.customer_name">
            <h2 class="text-center text-xl font-bold" >Customer: {{order.customer_name}}</h2>               
        </div>
        <div class="relative flex pb-2 space-x-5 border-b border-gray-200 lg:space-x-3 xl:space-x-5">
                <div class="w-full flex justify-between py-1">
                    <div class="h-8 w-12 text-lg bg-indigo-500 font-bold text-white rounded-xl" > {{order.remaining_time}}</div>
                    <h4 class=" font-bold">Order: {{order.check_number}}</h4>
                    <p class=" text-gray-500">
                        Staff: {{order.open_employee_name}}
                    </p>
            </div>                   
        </div>  
        
        <!-- Extra Infos -->
        <div class="relative flex flex-col font-medium">
            <!-- {{test()}} -->
            Order Time: {{(order.order_start_time != null ) ? formatTheDateToTime(order.order_start_time) : ''}}
        </div>
        <!-- <div class="relative flex flex-col font-medium">
            
            PickUP Time: {{formatTheDateToTime(order.pickup_time)}}
        </div> -->
        <div class="relative flex flex-col text-sm">
        <div v-for="item in order.order_extra_infos" :key="item.order_detail_id" >
            <div class="rounded-sm shadow-sm">
                <!-- <div class="flex justify-start items-center gap-2"> -->
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"></path></svg> -->
                    <!-- <div class="h-8 w-8 text-lg bg-green-500 font-bold text-white" > {{item.quantity}} </div> -->
                    <!-- <div> -->
                        <span class="text-gray-700 font-bold tracking-wider">{{item.menu_item_name}}</span>
                        
                        <span class="text-gray-600 text-sm font-medium" v-if="item.description"> :  {{item.description}}</span>
                        <!-- <template v-if="count(item.condiments) > 0" > -->
                        <template v-if="item.condiments.length > 0">
                            :
                        <span v-for="condiment in item.condiments" :key="condiment.order_detail_id" class="text-gray-600 text-sm font-medium" >
                            {{condiment.menu_item_name}}
                        </span>
                        </template>

                            
                        <!-- </tempate> -->
                    <!-- </div> -->
                <!-- </div> -->
                <!-- <span class="font-bold text-yellow-500">+28%</span> -->
            </div>         
        </div> 
        </div> 

        <!-- Item -->
        <div class="relative border border-gray-700 bg-white flex flex-col text-sm">
        <div v-for="item in order.order_details" :key="item.order_detail_id" class="relative flex flex-col ">
            <div class="border border-gray-200 flex justify-between  p-1 rounded-sm shadow-sm" 
            :class="item.return_time ? 'bg-red-300': ''"
            >
                <div class="flex justify-start  gap-2">
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"></path></svg> -->
                    <div class="text-lg font-bold" > {{item.quantity}}                   
                    </div>
                    <div class="text-gray-500 font-medium text-sm">
                        {{item.unit}} 
                    </div>
                    <div>
                        <p class="text-gray-700 font-bold tracking-wider">{{item.menu_item_name}}</p>
                        <p class="text-gray-500 text-sm font-medium">{{item.description}}</p>
                        <!-- <template v-if="count(item.condiments) > 0" > -->
                            <template v-if="item.condiments.length > 0">
                            <p v-for="condiment in item.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                {{condiment.menu_item_name}}
                            </p>
                            </template>

                            
                        <!-- </tempate> -->
                    </div>
                </div>
                <!-- <span class="font-bold text-yellow-500">+28%</span> -->
            </div>         
        </div>  
        </div>
        <!-- Button -->
        <div class="relative flex mt-2">   
            <a href="#_"  @click.prevent = "completeOrder(selected)"  class="relative flex items-center justify-center px-3 py-3 font-medium text-white rounded-xl group mr-1">
                <span class="w-full h-full absolute inset-0  rounded-xl bg-yellow-500"></span>
                <span class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-xl"></span>
                <span class="relative">Complete</span>
                <!-- <svg class="w-5 h-5 ml-2 transition-all duration-200 ease-out transform group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
            </a>
            <a href="#_" class="relative flex items-center justify-center px-3 py-3 font-medium text-white rounded-xl group">
                <span class="w-full h-full absolute inset-0  rounded-xl bg-green-500"></span>
                <span class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-xl"></span>
                <span class="relative">Choose Plan</span>
                <!-- <svg class="w-5 h-5 ml-2 transition-all duration-200 ease-out transform group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
            </a>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import {mapGetters, mapActions} from 'vuex'
import axios from 'axios'

export default {
    
    name: 'order',
    
    props: [
        'order'
    ],
    
    data () {
        return {
           
        }
    },
    computed: {
        // ...mapGetters(['editForm',]),
         ...mapGetters({
                getAuth: 'auth/getAuth'
            }),
    },
  
    components: [
    ],

     methods: {
       formatTheDateToDateTime(rawDate){
         return moment(String(rawDate)).format('DD/MM/YY hh:mm A')
        },  
       test(){
           var a = moment("2021-11-26 19:14:14");
           var b = moment("2021-11-26 19:13:14");
         return b.from(a);
        },  
       formatTheDateToTime(rawDate){
         return moment(String(rawDate)).format('hh:mm A')
        },  
    

        
      
    },
    mounted(){
   
    }
   
}
</script>

<style scoped>
     
</style>