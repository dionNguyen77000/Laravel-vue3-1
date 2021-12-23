<template>

<div class="mt-2">  
    <!-- {{selected}} -->
    <div class="px-4 mx-auto text-center max-w-7xl z-10">
         <div class="sticky top-0 bg-gray-500 justify-start md:justify-between flex flex-wrap
         p-1  z-10">

            <div class="collapse flex flex-wrap mb-1 sm:mb-0 ">
                 <div class="relative">              
                            <input 
                            class="h-8 w-10 border-gray-400 px-4"
                            type="checkbox"  
                                @change="toggleSelectAll"    
                                :checked="filteredRecords.length === selected.length"  
                            >
                        <!-- <svg class="w-5 h-5 ml-2 transition-all duration-200 ease-out transform group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
                
                </div>
                <div class="relative flex" v-if="selected.length">
                       <button  
                            class="h-10 w-12  ml-1 p-2 border border-gray-500 bg-yellow-700 hover:bg-yellow-900 shadow-md rounded-lg text-white focus:outline-none"
                            @click.prevent = "calculateOrders(selected)"
                        >
                            Add
                        </button>
                </div>

               <!-- delete with selected -->
                <div class="dropdown inline-block relative z-10"  v-if="selected.length">
                        <button 
                        class="border-blue-500 border bg-white text-gray-700 font-semibold py-1 px-1 inline-flex items-center"
                        @click.prevent="selected_dropdown_active = !selected_dropdown_active"
                        >
                        <span class="text-sm">W Select</span>
                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/> </svg>
                        </button>

                        <ul class="dropdown-menu absolute text-gray-700 pt-1"
                        :class="selected_dropdown_active ? 'block': 'hidden'"
                        >
                        <li><a class=" text-sm bg-yellow-200 hover:bg-yellow-500 hover:text-white py-1 px-6 block whitespace-no-wrap" href="#"  @click.prevent = "completeTheOrders(selected)">
                             {{ selected_preparation == 0 ?  'Make Done' : 'Make Undone ' }}
                        </a></li>
                       
                        </ul>
                       
                    </div>
            </div>     

          

                 <!-- Done And Undone Filter -->
            <div class=" relative flex" >
                <div class="h-8 w-16 relative">
                    <select v-model="selected_order_type" @change="fetchData"
                        class="appearance-none h-full border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option value="">
                            Order 
                        </option>
                        <option value="1244">
                            Take Away
                        </option>
                        <option value="1245" >
                            Delivery
                        </option>
                        <option value="1243" >
                            Dining
                        </option>    
                            <option value="1240" >
                            Counter Order
                        </option>                     
                        <option value="1239" >
                            Phone Order
                        </option>                                                                          
                        <option value="1241" >
                            Online Order
                        </option>                        
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
                
                <div class="h-8 w-20 relative">
                        <select v-model="selected_preparation" @change="fetchData"
                            class="appearance-none h-full border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="0">
                                Undone
                            </option>
                            <option value="1" >
                                Done
                            </option>
                            <option  value= '2'>Done&Undone</option>         
                        
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                </div>
            
                <div class="h-8 w-24 relative">
                    <select v-model="selected_order_status"
                        class="appearance-none h-full border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option value="">
                            Late&Cook
                        </option>
                        <option value="Late" >
                            Late
                        </option>
                        <option value="Cook" >
                            Cook Now
                        </option>        
                    
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
                <div class="h-8 w-16 relative">
                    <select v-model="selected_time" @change="fetchData"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option value='Today'> Today</option>
                        <option value='Yesterday'> Yesterday</option>
                        <option value='Two_days_Ago'> 2 days ago</option>
                        <option value="Current_Week">This Week</option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                 </div>

                <div class="h-8 w-24 relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                            <path
                                d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                            </path>
                        </svg>
                    </span>
                    <input v-model="quickSearchQuery" placeholder="Quick Search"
                        class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 border-b block pl-6 pr-6 py-1 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700" />
                </div>                 
            </div>
           
            <div  class="block relative flex  text-xs md:text-sm lg:text-lg">
                <div class=" xs:w-full">
                    <button @click="fetchData"
                    class=" mr-1 py-1 px-3 shadow-md rounded-full text-white focus:outline-none"
                    :class="selected.length >0 ? 'bg-gray-200': 'bg-green-500 hover:bg-green-700'"
                    >                  
                        Load  
                    </button>
                    <button  @click="toogleAutoLoad"
                    class=" py-1 px-2 shadow-md rounded-full  text-white focus:outline-none"
                    :class="selected.length >0 ? 'bg-gray-200': 'bg-green-500 hover:bg-green-700'"                  
                    >
                        
                         {{ this.timer ?  'Stop Auto-L' : 'Auto-L ' }}
                    </button>
                </div>
            </div>
          

           
        </div>  
        <!-- {{selected}} -->
        <div class=" mt-2 grid gap-3 lg:grid-cols-4 md:grid-cols-3 sm:grid-cols-2">
            <!-- {{filteredRecords}} -->
                <!-- Single Order -->
                    <!-- <Post v-for="post in allPosts" :post="post" :key="post.id"> </Post> -->
                    <!-- <Order v-for="order in filteredRecords" :order="order" :key="order.order_head_id"> </Order> -->
                <div v-for="order in filteredRecords" :order="order" :key="order.order_head_id" class=" 
                text-xs  md:text-sm lg:text-lg
                relative flex flex-col rounded-2xl">
                    <!-- {{order}} -->
                    <div class="absolute inset-0 w-full h-full rounded-2xl bg-green-100"></div>
                    <div class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-2xl"
                           :class="{ 
                            'bg-red-400': order.remaining_time.includes('Late') ,
                            'bg-yellow-300': order.remaining_time < 15 ,
                        }"
                    ></div>
                   
                    <div class="relative flex pb-2 space-x-5 border-b border-gray-200 lg:space-x-3 xl:space-x-5">
                            <div class="w-full flex justify-between py-1">
                                <div class="ml-1 p-1 bg-green-800 font-bold text-white rounded-xl" > {{order.remaining_time}}'</div>
                                <h4 class=" p-1 font-bold text-white bg-indigo-500 ">{{order.check_number}}</h4>
                                <p class="mr-2 font-semibold text-gray-500">
                                    Staff: {{order.open_employee_name}}
                                </p>
                        </div>                   
                    </div>  

                     <div class="relative" v-if="order.check_name">
                        <h3 class="text-center font-bold" >{{order.check_name}}</h3>               
                    </div>
                     <div class="relative" v-if="order.delivery_info">
                        <h4 class="text-center font-bold" >{{order.delivery_info}}</h4>               
                    </div>
                    
                    <!-- Extra Infos -->
                    <div class="relative flex flex-col font-medium  mt-1 ">
                        Order Time: {{(order.order_start_time != null ) ? formatTheDateToDateTime(order.order_start_time) : ''}}
                    </div>
                    <!-- <div class="relative flex flex-col font-medium">           
                        PickUP Time: {{formatTheDateToTime(order.pickup_time)}}
                    </div> -->
                    
                    <div class="relative flex flex-col text-smmb-1">                 
                        <div v-for="item_familyGroup in order.order_details[0]"  :key="item_familyGroup">
                            <!-- Order Info section  -->                  
                            <div class="rounded-sm shadow-sm">
                                <span class="text-gray-700 font-bold tracking-wider"> {{item_familyGroup.menu_item_id}} {{item_familyGroup.menu_item_name}}</span>
                                
                                <span class="text-gray-600 text-sm font-medium" v-if="item_familyGroup.description"> :  {{item_familyGroup.description}}</span>
                                <!-- <template v-if="count(item.condiments) > 0" > -->
                                <template v-if="item_familyGroup.condiments.length > 0">
                                    :
                                <span v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-600 text-sm font-medium" >
                                   {{condiment.menu_item_name}}
                                </span>
                                </template>
                            </div> 
                        </div> 
                    </div>
                
                    <div class="relative my-2 bg-white flex flex-col">
                        <!-- DeepFried + Rice Section -->
                        <div                 
                            :class="{ 
                                'border-2 border-indigo-700': order.order_details.slice(1,3).flat().length > 0 ,
                            }"
                        >
                            <template v-for="item_familyGroups,index in order.order_details.slice(1,3)" :key="index" >  
                            <!-- Loop throug each family Group (chicken, beef, etc...) -->
                                <template v-for="item_familyGroup in item_familyGroups"  :key="item_familyGroup">
                                <div class="border border-gray-200  p-1 rounded-sm shadow-sm"    
                                 :class="{ 
                                        'bg-gray-300' : item_familyGroup.is_make==1,                                   
                                    }"                          
                                >
                                            <div class="flex justify-between  gap-2">
                                                <div class="font-bold" > 
                                                    {{item_familyGroup.return_time ? 'Void' : item_familyGroup.quantity}}                   
                                                </div>
                                                <div class="text-gray-500 font-medium">
                                                    {{item_familyGroup.unit}} 
                                                </div>                                          
                                                <div>                                              
                                                    <p class="text-gray-700 font-bold tracking-wider">                                                 
                                                        {{item_familyGroup.menu_item_name}}
                                                    </p>
                                                    <p class="text-gray-500 font-medium">{{item_familyGroup.description}}</p>
                                                        <template v-if="item_familyGroup.condiments.length > 0">
                                                        <p v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                                            {{condiment.menu_item_name}}
                                                        </p>
                                                        </template>                                    
                                                </div>
                                                <div class="">
                                                    <a href="#"  @click.prevent = "completeTheItem(item_familyGroup)" class="p-1 lg:p-2 shadow-xl rounded-lg bg-green-500 text-white text-xs hover:bg-green-700 focus:outline-none"> 
                                                    {{ item_familyGroup.is_make ? 'UnM' : 'Mark' }}
                                                    </a>
                                                </div>
                                                
                                            </div>
                                        </div>    
                                </template>                           
                            </template>
                        </div>
                    <!-- StirFry Section chicken, beef, to Family Pack, Lunch Special-->
                        <div :class="{ 
                                'border-2 border-indigo-700': order.order_details.slice(3,13).flat().length > 0 ,
                            }" >               
                            <template v-for="item_familyGroups,index in order.order_details.slice(3,13)" :key="index" >  
                                <!-- Loop throug each family Group (chicken, beef, to Family Pack, Lunch Special etc...) -->
                                <template v-for="item_familyGroup in item_familyGroups"  :key="item_familyGroup">
                                <div class="border border-gray-200  p-1 rounded-sm shadow-sm"    
                                 :class="{ 
                                        'bg-gray-300' : item_familyGroup.is_make==1,                                   
                                    }"                          
                                >
                                            <div class="flex justify-between  gap-2">
                                                <div class="font-bold" > 
                                                    {{item_familyGroup.return_time ? 'Void' : item_familyGroup.quantity}}                   
                                                </div>
                                                <div class="text-gray-500 font-medium">
                                                    {{item_familyGroup.unit}} 
                                                </div>                                          
                                                <div>                                              
                                                    <p class="text-gray-700 font-bold tracking-wider">                                                 
                                                        {{item_familyGroup.menu_item_name}}
                                                    </p>
                                                    <p class="text-gray-500 font-medium">{{item_familyGroup.description}}</p>
                                                        <template v-if="item_familyGroup.condiments.length > 0">
                                                        <p v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                                            {{condiment.menu_item_name}}
                                                        </p>
                                                        </template>                                    
                                                </div>
                                                <div class="">
                                                    <a href="#"  @click.prevent = "completeTheItem(item_familyGroup)" class="p-1 lg:p-2 shadow-xl rounded-lg bg-green-500 text-white text-xs hover:bg-green-700 focus:outline-none"> 
                                                    {{ item_familyGroup.is_make ? 'UnM' : 'Mark' }}
                                                    </a>
                                                </div>
                                                
                                            </div>
                                        </div>    
                                </template>                         
                            </template>
                        </div>
                    <!-- Miscellaneous -->
                        <div :class="{ 
                                'border-2 border-indigo-700': order.order_details.slice(13,14).flat().length > 0 ,
                            }" >               
                            <template v-for="item_familyGroups,index in order.order_details.slice(13,14)" :key="index" >  
                                <!-- Loop throug each family Group -->
                                <template v-for="item_familyGroup in item_familyGroups"  :key="item_familyGroup">
                                <div class="border border-gray-200  p-1 rounded-sm shadow-sm"    
                                 :class="{ 
                                        'bg-gray-300' : item_familyGroup.is_make==1,                                   
                                    }"                          
                                >
                                            <div class="flex justify-between  gap-2">
                                                <div class="font-bold" > 
                                                    {{item_familyGroup.return_time ? 'Void' : item_familyGroup.quantity}}                   
                                                </div>
                                                <div class="text-gray-500 font-medium">
                                                    {{item_familyGroup.unit}} 
                                                </div>                                          
                                                <div>                                              
                                                    <p class="text-gray-700 font-bold tracking-wider">                                                 
                                                        {{item_familyGroup.menu_item_name}}
                                                    </p>
                                                    <p class="text-gray-500 font-medium">{{item_familyGroup.description}}</p>
                                                        <template v-if="item_familyGroup.condiments.length > 0">
                                                        <p v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                                            {{condiment.menu_item_name}}
                                                        </p>
                                                        </template>                                    
                                                </div>
                                                <div class="">
                                                    <a href="#"  @click.prevent = "completeTheItem(item_familyGroup)" class="p-1 lg:p-2 shadow-xl rounded-lg bg-green-500 text-white text-xs hover:bg-green-700 focus:outline-none"> 
                                                    {{ item_familyGroup.is_make ? 'Un-M' : 'Mark' }}
                                                    </a>
                                                </div>
                                                
                                            </div>
                                        </div>    
                                </template>                         
                            </template>
                        </div>
                        <!--  Drinks, Gift Section -->
                        <div  :class="{ 
                                'border-2 border-indigo-700': order.order_details.slice(14).flat().length > 0 ,
                            }" >               
                            <template v-for="item_familyGroups,index in order.order_details.slice(14)" :key="index" >  
                                <!-- Loop throug each family Group (chicken, beef, etc...) -->
                                 <template v-for="item_familyGroup in item_familyGroups"  :key="item_familyGroup">
                                <div class="border border-gray-200  p-1 rounded-sm shadow-sm"    
                                 :class="{ 
                                        'bg-gray-300' : item_familyGroup.is_make==1,                                   
                                    }"                          
                                >
                                            <div class="flex justify-between  gap-2">
                                                <div class="font-bold" > 
                                                    {{item_familyGroup.return_time ? 'Void' : item_familyGroup.quantity}}                   
                                                </div>
                                                <div class="text-gray-500 font-medium">
                                                    {{item_familyGroup.unit}} 
                                                </div>                                          
                                                <div>                                              
                                                    <p class="text-gray-700 font-bold tracking-wider">                                                 
                                                        {{item_familyGroup.menu_item_name}}
                                                    </p>
                                                    <p class="text-gray-500 font-medium">{{item_familyGroup.description}}</p>
                                                        <template v-if="item_familyGroup.condiments.length > 0">
                                                        <p v-for="condiment in item_familyGroup.condiments" :key="condiment.order_detail_id" class="text-gray-500 text-sm font-medium" >
                                                            {{condiment.menu_item_name}}
                                                        </p>
                                                        </template>                                    
                                                </div>
                                                <div class="">
                                                    <a href="#"  @click.prevent = "completeTheItem(item_familyGroup)" class="p-1 lg:p-2 shadow-xl rounded-lg bg-green-500 text-white text-xs hover:bg-green-700 focus:outline-none"> 
                                                    {{ item_familyGroup.is_make ? 'Un-M' : 'Mark' }}
                                                    </a>
                                                </div>
                                                
                                            </div>
                                        </div>    
                                </template>                           
                            </template>
                        </div>

                    </div>                
                    <!-- Button -->
                    <div class="relative flex mt-auto mx-auto">            
                        <span class="relative flex items-center justify-center p-2 font-medium text-white rounded-xl group mr-1">
                            <span class="w-full h-full absolute inset-0  rounded-xl bg-yellow-500"></span>
                            <span class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-xl"></span>
                            <span class="relative">
                                <input class="w-8 h-8 lg:w-12 lg:h-12" type="checkbox"  
                                :value="order" v-model="selected" 
                                @change="selectOrder($event)" 
                                >
                            </span>
                            <!-- <svg class="w-5 h-5 ml-2 transition-all duration-200 ease-out transform group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
                        </span>
                        <a href="#_"  @click.prevent = "completeTheOrder(order)"  class="relative flex items-center justify-center p-2 font-medium text-white rounded-xl group mr-1">
                            <span class="w-full h-full absolute inset-0  rounded-xl bg-yellow-500"></span>
                            <span class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-xl"></span>
                            <span class="relative">
                                {{ order.is_make ? 'Undone' : 'Done' }}
                            </span>
                            <!-- <svg class="w-5 h-5 ml-2 transition-all duration-200 ease-out transform group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
                        </a>
                        <a href="#_" @click.prevent = "countItemsInTheOrder(order)" class="mr-1 relative flex items-center justify-center p-2 font-medium text-white rounded-xl group">
                            <span class="w-full h-full absolute inset-0  rounded-xl bg-green-500"></span>
                            <span class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-xl"></span>
                            <span class="relative">Count</span>
                            <!-- <svg class="w-5 h-5 ml-2 transition-all duration-200 ease-out transform group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
                        </a>
                        <a href="#_" @click.prevent = "viewOrderDetail(order)" class="relative flex items-center justify-center p-2 font-medium text-white rounded-xl group">
                            <span class="w-full h-full absolute inset-0  rounded-xl bg-blue-500"></span>
                            <span class="absolute inset-0 w-full h-full border-2 border-gray-900 rounded-xl"></span>
                            <span class="relative">View</span>
                            <!-- <svg class="w-5 h-5 ml-2 transition-all duration-200 ease-out transform group-hover:translate-x-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
                        </a>
                    </div>
                </div>

                <!-- End First Plan -->

        </div>
    </div>
   
     <div class="px-4 mx-auto text-center max-w-7xl" v-if="clickOderModalId">
        <Order_Modal  
            :combinedOrderDetailsOfTwoOrders="combinedOrderDetailsOfTwoOrders"                           
            :selectedOrderNumbers="this.selectedOrderNumbers"                           
            @close="clickOderModalId=null" 
        />
        <!-- :requiredRecords="requiredRecords" 
        :categoryOptions="response.categoryOptions"
        :unitOptions="response.unitOptions"
        :custom_columns="response.custom_columns"
        :selectedSupplierInfo ="selectedSupplierInfo" -->
    </div>
</div>
</template>

<script>
import moment from 'moment'
import auth from '../../router/middleware/auth'
import guest from '../../router/middleware/guest'
import redirectIfNotCustomer from '../../router/middleware/redirectIfNotCustomer'
import queryString from 'query-string' //use package query-string npm install query-string

import Order_Modal from './pos_modal/order_modal.vue'

// import PostForm from '../components/PostForm.vue'
import Order from './pos_components/order.vue'
import {mapGetters, mapActions} from 'vuex'
export default {
    name: 'App',
      middleware: [
            auth
        ],
        components:{
            Order_Modal
            // PostForm, Post, Pagination
        },
      
    data(){
        return {
            user: window.User,
            response:{},
            orders: {},
            timer: null, 
            isPause: false,
            sort: {
                key: 'order_start_time',
                order: 'desc'
            }, 
             // Filter Section Settings
            quickSearchQuery: '',
            limit:"",
            selected_preparation:"0",
            selected_order_type:"",
            selected_order_status:"",
            selected_time:"Today",
            selected_dropdown_active: false,
            selected: [],
            slectedId: [],
            selectedOrderNumbers: null,

            clickOderModalId: null,

            combinedOrderDetailsOfTwoOrders: null,
            orderDetail: null,

            // selected_supplier: '',
            // selected_category: '',
            // selected_preparation: 'All',
            // selected_active: '1',
            // selected_location: 'All',
            // selected_permission: 'All',
            // selected_check: 'All',
        }
    },

    computed: {

        filteredRecords () {
            // return this.response.records;
            let data = this.response.orders;
            console.log(data)
                 
            // quick search query
            if(data){
            data = data.filter((row) => {
                return Object.keys(row).some((key) => {
                    return (
                        // quick serach query
                        String(row[key]).toLowerCase().indexOf(this.quickSearchQuery.toLowerCase()) > -1 
                        )
                })
            })
            }

            // filter Late/cook/notcook
            if(this.selected_order_status && data){
                data = data.filter((row) => {
                    return Object.keys(row).some((key) => {
                        var is_return = false
                        let order_remaining_time = row['remaining_time']
                        console.log(order_remaining_time)
                        switch (this.selected_order_status) {
                            case "Late":
                                if(order_remaining_time.includes(this.selected_order_status)){
                                    is_return = true
                                }
                                break;
                            case "Cook":
                                if(order_remaining_time < 15){
                                    is_return = true
                                }
                                break;                    
                            default:
                                break;
                        }
                      
                       
                        return is_return
                    })
                })
            }
            if(this.selected_order_type && data){
                data = data.filter((row) => {
                    return Object.keys(row).some((key) => {
                        var is_return = false
                        let orderInfo_details = row['order_details'][0]
                        for (let index = 0; index < orderInfo_details.length; index++) {
                            const eachItemInOrderInfo = orderInfo_details[index];
                                if(eachItemInOrderInfo.menu_item_id == this.selected_order_type) {
                                    is_return = true
                                    break;                           
                                }                     
                        }                    
                        return is_return
                    })
                })
            }
                    
            //  sort data according to clicking the head column
            if (this.sort.key) {
                data = _.orderBy(data, (i) => { //lodash 
                    
                    let value = i[this.sort.key]                    
                    if (!isNaN(parseFloat(value)) && isFinite(value)) {
                        return parseFloat(value)
                    }

                    return String(i[this.sort.key]).toLowerCase()
                }, this.sort.order)
            }
            return data
        },
       
    },
    methods: {
        ...mapActions(['fetchPosts']),
        fetchData() {
            // if(this.selected.length>0){
            // alert('Please unselect all orders before auto-loading')
            // return;
            // }
           
            return axios
            .get(`/api/orders?${this.getQueryParameters()}`)
            .then(response => {
                this.response= response.data.data;
                this.selected=[]
            })
        },
     
        getQueryParameters() {
            return queryString.stringify({
                limit: this.limit,
                preparation: this.selected_preparation,     
                // order_status: this.selected_order_status,     
                selected_time: this.selected_time,     

                // ...this.search
            }, 
            )           
        },
        toggleSelectAll () {
             if(this.selected.length > 0){
                this.cancelAutoUpdate()
            }
            if (this.selected.length == this.filteredRecords.length) {
                this.selected = []
                return
            }

            this.selected = this.filteredRecords
        },

        formatTheDateToDateTime(rawDate){
        return moment(String(rawDate)).format('DD/MM/YY hh:mm A')
        },  
      
       formatTheDateToTime(rawDate){
         return moment(String(rawDate)).format('hh:mm A')
        },  
        completeTheOrder(order){
            // if(order.is_make=="1"){
            //     if(!window.confirm(`Undo Order ` + order.check_number + ' ?')){
            //         return
            //     }
            // }else {
            //     if(!window.confirm(`Complete Order ` + order.check_number + ' ?')){
            //        return
            //     }
            // }

            order.is_make=1
            // this.isLoading = true
            axios.post(`/api/orders/completeOrder/${order.order_head_id}`).then((response) => {
                // this.isLoading = false

                this.fetchData().then(() => {
                    // this.editing.currentQtyId = null
                    // this.editing.form = null
                        
                })

                
            })
        },
        completeTheOrders(selectedOrders){
            if(this.selected_preparation == 0){
                if(!window.confirm(`Done selected Orders . Proceed ?`)){
                    return
                }
            }else if(this.selected_preparation == 1) {
                if(!window.confirm(`Undone selected Order . Proceed ?`)){
                    return
                }
            } else return

            var selectedIds = []
            selectedOrders.forEach(order => {
                selectedIds.push(order.order_head_id)
            });
            // this.isLoading = true
            axios.post(`/api/orders/completeOrders/${selectedIds}`).then((response) => {
                // this.isLoading = false

                this.fetchData().then(() => {
                        // this.editing.currentQtyId = null
                        // this.editing.form = null

                        this.selected=[]
                })

                
            })
           
        },
        completeTheItem(order_detail){
            if(order_detail.is_make == 0 || order_detail.is_make==null){

                order_detail.is_make = 1
            } else {
                order_detail.is_make = 0
            }
            // this.isLoading = true
            axios.post(`/api/orders/completeTheItem/${order_detail.order_detail_id}`).then((response) => {
                // this.isLoading = false

            // this.fetchData().then(() => {
            //         // this.editing.currentQtyId = null
            //         // this.editing.form = null
                        
            //     })

                
            })
        },
        countItemsInTheOrder(order){
           
            var allFamilyGroups = order.order_details;
           

            var totalOfItems = 0;
            var totalOfKitchenItems = 0;
            var totalOfOtusideItems = 0;
            var totalOfMarkedItems = 0;

            for (let index = 1; index < allFamilyGroups.length; index++) {
                if (index <=13) {
                    let theGroup = allFamilyGroups[index]
                    theGroup.forEach(item => {
                        totalOfItems = totalOfItems + item.quantity
                        totalOfKitchenItems = totalOfKitchenItems + item.quantity
                        if(item.is_make==1){
                            totalOfMarkedItems =totalOfMarkedItems + item.quantity
                        }
                    });
                } else {
                    let theGroup = allFamilyGroups[index]
                    theGroup.forEach(item => {
                        totalOfItems = totalOfItems + item.quantity
                        totalOfOtusideItems = totalOfOtusideItems + item.quantity
                        if(item.is_make==1){
                            totalOfMarkedItems =totalOfMarkedItems + item.quantity
                        }
                    });
                }
                
            }

        
            alert ('Total  Items : ' + totalOfItems + '\r\n' +
            'Kitchen Items: ' + totalOfKitchenItems + '\r\n' +
            'Outside Items: ' + totalOfOtusideItems + '\r\n' +
            'Marked Items: ' + totalOfMarkedItems + '\r\n'          
            )
        },
        
        calculateTwoOrders(familyGroupsOfOrder1,familyGroupsOfOrder2){
           
            // loop through each family Groups of Order 1
            for (let index = 0; index < familyGroupsOfOrder1.length; index++) {
                // console.log('index is: ' + index)
                //get family groups number Index of order 1
                var combinedFamilyGroupsOfTwoOrders = familyGroupsOfOrder1[index];
                //  console.log('frist group couunt: ' + combinedFamilyGroupsOfTwoOrders.length)
                // console.log(combinedFamilyGroupsOfTwoOrders)

                //get family groups number Index of order 2
                var theFamilyGroupOfOrder2 = familyGroupsOfOrder2[index];
                //  console.log('second group couunt: ' + theFamilyGroupOfOrder2.length)
                // now we need to compare each group of order 1 and order 2

                // loop through all item of each familyGroup in order 2            
                theFamilyGroupOfOrder2.forEach(theItemInTheFamilyGroupOfOrder2 => {

                    // console.log('item in order 2 is '+theItemInTheFamilyGroupOfOrder2.menu_item_name)

                    var itemAlreadyExit = 0;
                    // check if  each item in familyGroup of ORder 2 already exit in the corresponding famimly group of order 1
                    // if already exit, increase the quantity, if not add that item from family gorup of order 2 to family group of order 1

                    for (let j = 0; j < combinedFamilyGroupsOfTwoOrders.length; j++) {
                        const theItemInTheFamilyGroupOfOrder1 = combinedFamilyGroupsOfTwoOrders[j];
                        // console.log('item in order 1 is '+theItemInTheFamilyGroupOfOrder1.menu_item_name)

                        //check matching name, unit and discription
                        if(theItemInTheFamilyGroupOfOrder1.menu_item_name == theItemInTheFamilyGroupOfOrder2.menu_item_name 
                            && theItemInTheFamilyGroupOfOrder1.unit == theItemInTheFamilyGroupOfOrder2.unit 
                            && theItemInTheFamilyGroupOfOrder1.description == theItemInTheFamilyGroupOfOrder2.description){
                            //if matching  matching name, unit and discription, continue to check if the same condiments

                            //if condiments of both are empty, no need to go further --> conclude item already exit
                            if(theItemInTheFamilyGroupOfOrder1.condiments.length == 0 && theItemInTheFamilyGroupOfOrder2.condiments.length == 0){
                                theItemInTheFamilyGroupOfOrder1.quantity = theItemInTheFamilyGroupOfOrder1.quantity + theItemInTheFamilyGroupOfOrder2.quantity;
                                itemAlreadyExit =1;
                                break;
                            } 
                            // if condiments of at leas 1 is not empty
                            else if (theItemInTheFamilyGroupOfOrder1.condiments.length > 0 && theItemInTheFamilyGroupOfOrder2.condiments.length == theItemInTheFamilyGroupOfOrder1.condiments.length) {
                                matchCondiments = 1;
                                for (k=0; k < count(theItemInTheFamilyGroupOfOrder2.condiments) ; k++) { 
                                    condimentOftheOrderDetail_k = theItemInTheFamilyGroupOfOrder2.condiments[k];
                                    condimentOfCurrentItemInArray_k = theItemInTheFamilyGroupOfOrder1.condiments[k];

                                    if(condimentOftheOrderDetail_k.menu_item_name != condimentOfCurrentItemInArray_k.menu_item_name){
                                        matchCondiments = 0;
                                        break;
                                    }
}                                
                                //if the condiments is also match, then 
                                if ( matchCondiments == 1){
                                    theItemInTheFamilyGroupOfOrder1.quantity = theItemInTheFamilyGroupOfOrder1.quantity + theItemInTheFamilyGroupOfOrder2.quantity;
                                    itemAlreadyExit =1;
                                    break;
                                }
                            }                 


                        } 
                                       
                    }

                    // after loop through all items in order 1 to compare,
                    // if item is not existing
                    if (itemAlreadyExit == 0) {
                        combinedFamilyGroupsOfTwoOrders.push(theItemInTheFamilyGroupOfOrder2)
                    }
                    //   console.table(combinedFamilyGroupsOfTwoOrders)
                })
                
            }
            return familyGroupsOfOrder1
        },
        calculateOrders(selected){
            this.combinedOrderDetailsOfTwoOrders = null
            this.selectedOrderNumbers = null
            this.clickOderModalId = selected;          
            if(selected.length > 1){
                // make copy of two array
                 var the_first_order_details = JSON.parse(JSON.stringify(selected[0].order_details))
                 var the_second_order_details = JSON.parse(JSON.stringify(selected[1].order_details))
                 this.selectedOrderNumbers = selected[0].check_number + "+" + selected[1].check_number
                            //  console.log('familyGroupsOfOrder1 '+familyGroupsOfOrder1.order_details[1][0])

                // var first_target_copy = Object.assign({}, the_first_order_details);
                this.combinedOrderDetailsOfTwoOrders = this.calculateTwoOrders(the_first_order_details,the_second_order_details)
                if (selected.length > 2){
                    for (let index = 2; index < selected.length; index++) {
                         this.combinedOrderDetailsOfTwoOrders = this.calculateTwoOrders(this.combinedOrderDetailsOfTwoOrders,selected[index].order_details)              
                         this.selectedOrderNumbers = this.selectedOrderNumbers + "+" + selected[index].check_number
                    }
                } 
            }
            return this.combinedOrderDetailsOfTwoOrders
            
        },
        viewOrderDetail(order){
            this.combinedOrderDetailsOfTwoOrders = null
            this.combinedOrderDetailsOfTwoOrders = JSON.parse(JSON.stringify(order.order_details)) 
            this.selectedOrderNumbers = order.check_number
            this.clickOderModalId = order       
            // return this.combinedOrderDetailsOfTwoOrders               
        },

        cancelAutoUpdate () {        
            clearInterval(this.timer);
            this.timer = null
        },

        startAutoUpdate(){
            this.timer = setInterval(this.fetchData,120000);  
        },

        selectOrder(e){
            if(this.selected.length>0){
                this.cancelAutoUpdate()
            }
            if(this.selected.length==0){
                this.startAutoUpdate()
            }
        },

        toogleAutoLoad(){
             if(this.selected.length>0){
                alert('Please unselect all orders before auto-loading')
                return;
            }

            if(this.selected.length==0){
                if(this.timer){
                    this.cancelAutoUpdate()
                }else this.startAutoUpdate()
            }
        }

        


    },
    mounted() {  
        this.fetchData()
        this.startAutoUpdate()           
    },

    unmounted(){
         this.cancelAutoUpdate();
    },
    // beforeUnmount(){
    //      alert('beofre unmount')
    //      this.cancelAutoUpdate();
    // },

  
}
</script>

<style scoped>


</style>