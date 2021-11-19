<template>
  <div id="delivery_journey" class="p-6"> 
        <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center rounded-lg shadow-md">
            <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :is-full-page="fullPage"/>       
            <div class="w-full p-1">
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Deilvery Journey</div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    v-if="response.allow.creation"
                    @click.prevent="toggleNewRecord">
                    {{ creating.active ? 'Hide' : 'New Delivery' }}
                    </a>
                </div>               
            </div>
            <!-- New Record section  -->
            <div class="flex justify-center" v-if="response.allow.creation && creating.active">
                <div class="w-full p-6 rounded-lg">
                <!-- <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">New Journey</h3> -->
                    <!-- <form action="#" @submit.prevent="store">
                       
                        <div class="mb-2" v-for="column in response.updatable" :key="column" >

                           <template v-if="column=='date'">
                            <label :for="column" class="sr-only"> </label>
                            <input type="date" :name="column" :id="column" :placeholder="column" class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                             v-model="creating.form[column]" disabled>
                            <div class="text-red-500 mt-2 text-sm" v-if="creating.errors[column]">
                                    <strong>{{ creating.errors[column][0] }}</strong>
                            </div>
                            </template>  
                            <template v-else>  
                            <label :for="column" class="sr-only"> </label>
                            <input type="text" :name="column" :id="column" :placeholder="column" class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                            :class="{ 'border-red-500': creating.errors[column] }"
                            v-model="creating.form[column]">
                            <div class="text-red-500 mt-2 text-sm" v-if="creating.errors[column]">
                                    <strong>{{ creating.errors[column][0] }}</strong>
                            </div>
                            </template>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-800 text-white px-4 py-2 rounded">Create</button>
                        </div>
                       
                    
                    </form> -->
             <form action="#" @submit.prevent="store">
                    <div class="text-center">
                    <button type="button" @click="addDestination" 
                    class="bg-yellow-500 hover:bg-yellow-800 text-white px-1 text-sm p-1 mb-1 rounded">
                        Add Destination
                    </button>
                    </div>
                    <div class="text-center"> 
                        <label class="font-semibold" :for="departure">Departure : </label>
                        <input 
                        class="bg-gray-100 border-2 p-1 rounded-lg" 
                         :class="{ 
                            'bg-pink-200' : !getAuth.isFirstLevelUser || !getAuth.isSecondLevelUser || !getAuth.isThirdLevelUser,
                        }"
                        id='departure' 
                        type="datetime-local"
                        :disabled = "!getAuth.isFirstLevelUser || !getAuth.isSecondLevelUser || !getAuth.isThirdLevelUser"
                        v-model="creating.form.departure"> 
                    </div>
                    <div class="mb-1" v-for="delivery_detail in delivery_details" :key="delivery_detail.id">
                        <div :id="delivery_detail.id" class="border border-gray-400 shadow">

                            <div class="p-2 flex justify-between flex justify-between">
                                <h4 class="text-green-400 font-bold ">{{delivery_detail.id}}</h4>
                                <button  type="button"
                                class="p-1 shadow-md rounded-full bg-red-400 text-white text-sm hover:bg-red-700 focus:outline-none"
                                 @click="removeDestination(delivery_detail.index)">
                                 <!-- Delte Hero Icon -->
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                                 </button>             
                            </div>
                            <div class="flex flex-wrap justify-center pb-1">
                                <div class="w-36 md:w-48 lg:w-56 m-0.5">
                                <label class="font-semibold" :for="delivery_detail.order_number">Order Number </label>
                                <input type="text"  
                                :class="{'border-red-500': creating.errors[delivery_detail.order_number] }"
                                placeholder="order number" 
                                class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                                v-model="delivery_detail.order_number">
                                <!-- <div class="text-red-500 mt-2 text-sm" v-if="creating.errors[delivery_detail.order_number]">
                                        <strong>{{ creating.errors[delivery_detail.order_number][0] }}</strong>
                                </div> -->
                                </div>


                           
                                <div class="w-36 md:w-48 lg:w-56 m-1">
                                    <label class="font-semibold" :for="delivery_detail.zone">Zone </label>

                                    <select v-model="delivery_detail.zone"
                                    class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                                    :class="{ 'border-red-500': creating.errors[delivery_detail.zone] }"
                                    >
                                        <option :value="option.zone" v-for="option,index in response.zoneOptions" :key="option">
                                            {{option.zone}}
                                        </option>
                                    </select>

                                </div>
                            
                                <div class="w-36 md:w-48 lg:w-56 m-1">
                                <label class="font-semibold" :for="delivery_detail.customer_payment">Carried Change </label>
                                <input type="number" step=".01" min="0.00"
                                    placeholder="carried change" 
                                    class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                                :class="{ 'border-red-500': creating.errors[delivery_detail.carried_change] }"
                                v-model="delivery_detail.change">
                                </div>

                                <div class="w-36 md:w-48 lg:w-56 m-1">
                                <label class="font-semibold" :for="delivery_detail.customer_payment">Customer Pay Cash</label>
                                <input type="number" step=".01" min="0.00"
                                    placeholder="cash paid by customer" 
                                    class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                                :class="{ 'border-red-500': creating.errors[delivery_detail.customer_payment] }"
                                v-model="delivery_detail.cash_received">
                                </div>
                            </div>
                            
                          
                        </div>
                    </div>
                    
                    
                    <div class="text-center">
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-800 text-white px-2 py-2 rounded">Create Journey</button>
                    </div>
                       
                        
                    <pre>{{ $data.delivery_details }}</pre>
                </form>

                 
        </div>
        
          

                
        </div>  <!-- End New Record Section -->


        <!-- progress step bar -->
        <div v-for="OG_Journey in response.onGoingJourneys" class="w-11/12 mx-auto my-2 border-2 border-green-600 p-3">	
            <!-- {{OG_Journey}} -->
            <!-- circle destination pots -->
          
            <h2 class="text-center text-lg font-semibold pb-1">Driver: {{OG_Journey.driver}} ({{OG_Journey.mobile}})</h2>
          
            <div class="flex pb-1">
                <div class="flex-1">
                </div>

                <div class="flex-1">
                    <div class="w-10 h-10 bg-yellow-500 mx-auto rounded-full text-lg text-white flex items-center">
                        <span class="text-white text-center w-full">
                            GLY<i class="fa fa-check w-full fill-current white"></i>
                        </span>
                    </div>
                    <div class=" mx-auto rounded-full text-lg flex items-center">
                        <span class=" text-xs text-center w-full">
                         {{(OG_Journey.delivery_details[0].departure != null ) ? formatTheDateToHourMinute(OG_Journey.delivery_details[0].departure) : ''}}
                        </span>
                    </div>
                </div>

            <template  v-for="OG_delivery_detail in OG_Journey.delivery_details" :key="OG_delivery_detail">
                <div class="mt-2 w-1/6  h-7 align-middle flex">
                    <div class="w-full bg-gray-300 rounded items-center align-middle align-center flex-1">
                        <div class=" bg-gray-300 text-xs leading-none py-1 text-center text-gray-700" style="width: 100%"
                        :class="{ 'bg-green-400 ': OG_delivery_detail.actual_arrival != null }"
                        ></div>
                        <div class=" mx-auto text-lg flex items-center"
                        :class="{ 'bg-green-400 ': OG_delivery_detail.actual_arrival != null }"
                        >
                            <span class=" text-xs text-center w-full">
                            {{(OG_delivery_detail.estimated_duration_arrival != null ) ? OG_delivery_detail.estimated_duration_arrival : ''}} m
                            </span>
                        </div>
                    </div>
                </div>
            
                
                <div class="flex-1">
                    <div class="w-10 h-10 bg-green-700 mx-auto rounded-full text-lg text-white flex items-center">
                        <span class="text-white text-center w-full">
                            {{OG_delivery_detail.zone}}
                            <i class="fa fa-check w-full fill-current white"></i>
                            </span>
                    </div>

                    <div class=" mx-auto rounded-full text-lg flex items-center">
                        <span class=" text-xs text-center w-full">
                        {{(OG_delivery_detail.estimated_arrival != null ) ? formatTheDateToHourMinute(OG_delivery_detail.estimated_arrival) : ''}}
                        </span>
                    </div>
                    <div class=" mx-auto rounded-full text-lg flex items-center">
                        <span class=" font-semibold text-xs text-center w-full">
                        {{(OG_delivery_detail.actual_arrival != null ) ? formatTheDateToHourMinute(OG_delivery_detail.actual_arrival) : ''}}
                        </span>
                    </div>
                </div>
            </template>

            <div class="mt-2 w-1/6  h-7 align-middle flex">
                <div class="w-full bg-gray-300 rounded items-center align-middle align-center flex-1">
                    <div class="  bg-gray-300 text-xs leading-none py-1 text-center text-gray-700 rounded" style="width: 100%"
                    ></div>
                    <div class=" mx-auto rounded-full text-lg flex items-center">
                        <span class=" text-xs text-center w-full">
                        {{(OG_Journey.delivery_details[OG_Journey.delivery_details.length-1].estimated_duration_return != null ) ? OG_Journey.delivery_details[OG_Journey.delivery_details.length-1].estimated_duration_return : ''}} m
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex-1">
                 <div class="w-10 h-10 bg-yellow-500 mx-auto rounded-full text-lg text-white flex items-center">
                        <span class="text-white text-center w-full">
                            GLY<i class="fa fa-check w-full fill-current white"></i>
                        </span>
                    </div>
                    <div class=" mx-auto rounded-full text-lg flex items-center">
                        <span class=" font-bold text-red-600 text-xs text-center w-full">
                         {{(OG_Journey.delivery_details[OG_Journey.delivery_details.length-1].estimated_return != null ) ? formatTheDateToHourMinute(OG_Journey.delivery_details[OG_Journey.delivery_details.length-1].estimated_return) : ''}}
                        </span>
                    </div>
            </div>	

            </div>
         
        </div>
        
        <!-- show hide column section -->
        <div id="show_hide_section" class="text-center mx-4 space-y-2">
            <p> <b>Show Hide Column </b></p>
            <ul id="hide_show_column_section" class="width-3/4 flex flex-wrap justify-center">
                <li  class="mr-3" v-for="column in response.displayable" :key="column">
                    <input type="checkbox" 
                    :value="column" 
                    :id="column" 
                    :checked="hideColumns.includes(column)"
                    v-model="hideColumns">
                    {{response.custom_columns[column] || column}}
                </li>
            </ul>         
        </div>

          <!-- advanced filter section -->
        <div class="mt-2 mb-2 p-2 shadow bg-gray-100 border border-gray-200">
        <a href="#" 
        class="p-2 m-1 inline-block text-blue-500 transition duration-300  hover:text-blue-700 focus:outline-none"
        @click.prevent="filter.active = !filter.active">
        {{ filter.active ? 'Hide Filter' : 'Advanced Filter' }}
        </a>
        
        <!-- advanced filter -->
        <form v-if="filter.active" 
         action="#" @submit.prevent="getRecords">
        <div class="mt-1 flex sm:flex-row flex-col">
            <div class="flex flex-row mb-1 sm:mb-0">
                <div class="relative mr-2">
                    <select v-model="search.column" 
                        class="p-2 appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option :value="column" v-for="column in advancedFilterColumns" :key="column">{{ column }}</option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex flex-row mb-1 sm:mb-0">
                <div class="relative mr-2">
                    <select v-model="search.operator"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    >
                        <option value="equals">=</option>
                        <option value="contains">contains</option>
                        <option value="starts_with">starts with</option>
                        <option value="ends_with">ends with</option>
                        <option value="greater_than">></option>
                        <option value="less_than"> &lt; </option>
                        <option value="greater_than_or_equal_to">>=</option>
                        <option value="less_than_or_equal_to"> &lt;=</option>
                    </select>
                     <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex flex-row mb-1 sm:mb-0">
                   <input v-if="search.column=='date'"
                    type="datetime-local" 
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    v-model="search.value" 
                    >
                    <input v-else
                    type="text" id="search" v-model="search.value"
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    >       

            </div>
        </div>
        <div class="mt-1 flex sm:flex-row flex-col">
            <div class="flex flex-row mb-1 sm:mb-0">
                <div class="relative mr-2">
                    <select v-model="search.column_1" 
                        class="p-2 appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option :value="column" v-for="column in advancedFilterColumns" :key="column">{{ column }}</option>

                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex flex-row mb-1 sm:mb-0">
                <div class="relative mr-2">
                    <select v-model="search.operator_1"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    >
                        <option value="equals">=</option>
                        <option value="contains">contains</option>
                        <option value="starts_with">starts with</option>
                        <option value="ends_with">ends with</option>
                        <option value="greater_than">></option>
                        <option value="less_than"> &lt; </option>
                        <option value="greater_than_or_equal_to">>=</option>
                        <option value="less_than_or_equal_to"> &lt;=</option>
                    </select>
                     <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex flex-row mb-1 sm:mb-0">
                <div class="relative mr-2">
                    
                    <input v-if="search.column_1=='date'"
                    type="datetime-local" 
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    v-model="search.value_1" 
                    >
                    <input v-else
                    type="text" id="search" v-model="search.value_1"
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    >                  
                </div>
            </div>

            <div class="flex flex-row mb-1 sm:mb-0">
                <div class="relative">
                       
                    <button 
                    class="px-8 pl-4 appearance-none h-full rounded border appearance-none w-full bg-green-500 hover:bg-yellow-700 text-white border-gray-400 leading-tight focus:outline-none focus:border-gray-500"
                    type="submit">
                    Filter
                    </button>                      
                </div>
            </div>
        </div>
       
        </form>      
       </div>

        <div class="flex sm:flex-row flex-col">
            <div class="flex flex-row mb-1 sm:mb-0">
               <!-- delete with selected -->
                    <div class="dropdown inline-block relative z-10"  v-if="selected.length">
                        <button 
                        class="border-blue-500 border horver:bg-blue-700 text-gray-700 font-semibold py-1 px-1 inline-flex items-center"
                        @click.prevent="selected_dropdown_active = !selected_dropdown_active"
                        >
                        <span class="text-sm">With Selected</span>
                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/> </svg>
                        </button>
                        <ul class="dropdown-menu absolute text-gray-700 pt-1"
                        :class="selected_dropdown_active ? 'block': 'hidden'"
                        >
                        <li><a class=" text-sm bg-blue-200 hover:bg-blue-700 hover:text-white py-1 px-6 block whitespace-no-wrap" href="#" @click.prevent = "destroy(selected)">Delete</a></li>
                        <li><a class=" text-sm bg-blue-200 hover:bg-blue-700 hover:text-white py-1 px-6 block whitespace-no-wrap" href="#" @click.prevent = "sumFuel(selected)">Sum Fuel</a></li>
                        </ul>
                    </div>

            
                <div class="relative">
                    <select v-model = "limit" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="">All</option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select v-model="selected_driver" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option  value= 'All'>Driver</option>         
                        <option v-for="option,index in response.userOptions" :value="option" :key="option">
                            {{option}} 
                        </option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
                <div class="relative">
                    <select v-model="selected_approval" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option  value= 'All'>Approval</option>                                
                        <option value="No"> No</option>
                        <option value="Yes">Yes</option>
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="block relative">
                <span class="h-full absolute inset-y-0 left-0 flex items-center pl-2">
                    <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                        <path
                            d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                        </path>
                    </svg>
                </span>
                <input v-model="quickSearchQuery" placeholder="Quick Search"
                    class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700" />
            </div>
        </div>

        <!-- <div class="justify-content-end mb-4">
            <a 
            class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
            @click.prevent="exportPDF()"
            >
                Export to PDF
            </a>
        </div> -->
     
        <!-- start Table -->
        
            <div  v-if="filteredRecords.length" class="bg-white shadow-md rounded my-3  overflow-x-auto">
                <table class="min-w-max w-full table-auto">
                <!-- Table Heading Section -->
                    <thead class="py-2">
                        <tr class="py-2 bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-2" 
                             v-if="(getAuth.isFirstLevelUser || getAuth.includesisSecondLevelUser) && canSelectItems"
                             >
                                    <input type="checkbox" 
                                    @change="toggleSelectAll" 
                                    :checked="filteredRecords.length === selected.length"
                                    >
                            </th>
                        <!--  Loop through column in response.displayable  -->
                            <template v-for="column in response.displayable" :key="column">
                            <!-- Table Heading - Columns not allow to show in any mode-->
                            <template v-if="unshownColumns.includes(column)">
                            </template>
                             <!-- Table Heading - Columns not allow to show according to the level of user-->
                            <template v-else-if="columnsNotAllowToShowAccordingToUserLevel.includes(column)">
                            </template>
                              <!-- Table Heading - Edit Mode-->
                            <template v-else-if="editing.id">
                                <!-- Table heading not shown in Edit Mode-->
                                <template v-if="unshownColumnsInEditMode.includes(column)
                                    || hideColumns.includes(column) ||!isUpdatable(column)">
                                </template>
                                <!-- Table heading shown in Edit Mode -->
                                <template v-else>
                                    <th class="text-left"
                                    :class="{ 'text-center': textCenterColumns.includes(column) }"
                                    >
                                        <span class="sortable" @click="sortBy(column)">{{response.custom_columns[column] || column}}</span>
                                        <div 
                                        class="arrow" 
                                        v-if="sort.key===column"
                                        :class="{ 'arrow--asc': sort.order === 'asc', 'arrow--desc': sort.order === 'desc'}">
                                        </div> 
                                    </th>
                                </template>
                            </template>
                            <!-- heading -not in edit mode-->
                            <template v-else>
                                <th  
                                class="text-left" 
                                :class="{ 'text-center': textCenterColumns.includes(column) 
                                }"
                                v-if="!hideColumns.includes(column)"
                                >
                                    <span class="sortable" @click="sortBy(column)">{{response.custom_columns[column] || column}}</span>
                                    <div 
                                    class="arrow" 
                                    v-if="sort.key===column"
                                    :class="{ 'arrow--asc': sort.order === 'asc', 'arrow--desc': sort.order === 'desc'}">
                                    </div> 
                                </th>
                            </template>
                            </template>
                            <th class="text-left">Actions </th>                          
                        </tr>
                    </thead>
                    <!-- End Table Heading -->
                     <!-- Row (Records) Section -->                    
                    <tbody class="text-gray-600 text-sm font-light">
                        <!-- Loop Through each records getting from controller -->
                        <tr v-for="record in filteredRecords" :key="record"  class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100"
                             :class="{ 'bg-red-300 hover:bg-red-400' : record.actual_return==null}"   
                        >                          
                            <td v-if="(getAuth.isFirstLevelUser || getAuth.includesisSecondLevelUser) && canSelectItems" 
                            class=" text-center">
                                <input type="checkbox" :value="record.id" v-model="selected">
                            </td>
                            <!-- Loop through each column-->
                            <template v-for="columnValue,column in record" :key="column">
                              <!-- Columns not allow to show in any mode-->
                            <template v-if="unshownColumns.includes(column)">
                            </template>
                            <!-- Columns not allow to show according to level of user-->                           
                            <template v-else-if="columnsNotAllowToShowAccordingToUserLevel.includes(column)">
                            </template>
                            <!-- Edit Mode-->
                            <template v-else-if="editing.id">  
                                 <!-- Edit Mode - column not show -->
                                <template v-if="unshownColumnsInEditMode.includes(column)
                                        || hideColumns.includes(column) || !isUpdatable(column)">
                                </template>
                                <!-- if the record currently edit -->               
                                <template v-else-if="editing.id === record.id">
                                     <!-- if column is date time format -->
                                    <template v-if="timeOnlyFormat.includes(column)"> 
                                    <td class="w-52 p-1 text-left"  
                                    v-if="response.displayable.includes(column)">
                                        <input type="datetime-local" 
                                        :name="column" :id="column"
                                        class="bg-gray-100 border-2 w-52 p-1 rounded-lg"
                                        :class="{ 
                                            'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column),
                                            'text-center': textCenterColumns.includes(column)}"
                                        v-model="editing.form[column]" 
                                        :disabled= "columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
                                        >

                                        <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                            <strong>{{ editing.errors[column][0] }}</strong>
                                        </span>         
                                    </td>                                                            
                                </template> 
                                <template v-else-if="dollarsSymbolColumns.includes(column)">
                                     <td class="w-16 p-1 text-left"  
                                    v-if="response.displayable.includes(column)"> 
                                        <input type="number" step=".01" min="0.00"
                                        class="w-16 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                        :class="{ 'border-3 border-red-700': editing.errors[column] ,
                                                'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column),
                                                'text-center': textCenterColumns.includes(column),
                                                }"
                                        
                                        v-model="editing.form[column]"

                                        :disabled= "columnsNotAllowToEditAccordingToUserLevel.includes(column)" 

                                        > 
                                        <br>
                                        <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                            <strong>{{ editing.errors[column][0] }}</strong>
                                        </span>
                                    </td>    
                                </template>
                            <template v-else-if="column=='approve'">
                                     <td class="w-16 p-1 text-left"  
                                    v-if="response.displayable.includes(column)"> 
                                    <select
                                    class='w-16 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    :class="{
                                        'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column),
                                        'text-center': textCenterColumns.includes(column),
                                    }"
                                    :name="column" :id="column" 
                                    v-model="editing.form[column]"                                  
                                    :disabled= "columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
                                    > 
                                            <option value="No">
                                              No
                                            </option>
                                            <option value="Yes">
                                              Yes
                                            </option>
                               </select>                                  
                                    </td>
                            </template> 
                             <template v-else-if="column=='driver'">
                                     <td class="w-16 p-1 text-left"  
                                    v-if="response.displayable.includes(column)"> 
                                    <select
                                    class='w-16 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    :class="{
                                        'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column),
                                        'text-center': textCenterColumns.includes(column),
                                    }"
                                    :name="column" :id="column" 
                                    v-model="editing.form[column]"                                  
                                    :disabled= "columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
                                    > 
                                           <option v-for="option,index in response.userOptions"  :value="option" :key="option">
                                                {{option}} 
                                            </option>
                               </select>                                  
                                    </td>
                                </template> 
                                <template v-else-if="column=='actual_return'">
                                 <input type="text" 
                                    class="w-24 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                    :class="{ 'border-3 border-red-700': editing.errors[column] ,
                                            'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column),
                                            'text-center': textCenterColumns.includes(column),
                                            }"
                                    
                                    v-model="editing.form[column]"

                                    :disabled= "columnsNotAllowToEditAccordingToUserLevel.includes(column)" 

                                    > 
                                </template>
                                
                                <template v-else>
                                <td class="w-24 p-1 text-left"  
                                v-if="response.displayable.includes(column)"> 
                                    <input type="text" 
                                    class="w-24 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                    :class="{ 'border-3 border-red-700': editing.errors[column] ,
                                            'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column),
                                            'text-center': textCenterColumns.includes(column),
                                            }"
                                    
                                    v-model="editing.form[column]"

                                    :disabled= "columnsNotAllowToEditAccordingToUserLevel.includes(column)" 

                                    > 
                                    <br>
                                    <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                        <strong>{{ editing.errors[column][0] }}</strong>
                                    </span>
                                </td>    
                            </template>
                                </template> <!-- End the row current edit -->
                                <!-- Edit Mode - for rows not currently edit -->
                                <template v-else>
                                </template> <!-- End Edit Mode - the rows current not edit -->
                            </template>  <!-- End Edit Mode -->
                            <!-- Not in Edit Mode-->
                            <template v-else>
                                 <template v-if="hideColumns.includes(column)">
                                </template>
                                
                                <template v-else>                                   
                                    <template v-if="dateOnlyFormat.includes(column)">
                                        <td class="p-1 text-left font-semibold"  
                                        :class="{ 'text-center': textCenterColumns.includes(column) }"
                                        v-if="response.displayable.includes(column)"> 
                                            {{(columnValue != null ) ? formatTheDate(columnValue) : ''}}
                                        </td>
                                    </template>
                                    <template v-else-if="timeOnlyFormat.includes(column)">
                                        <td class="p-1 text-left font-semibold"  
                                        :class="{ 'text-center': textCenterColumns.includes(column) }"
                                        v-if="response.displayable.includes(column)"> 
                                            {{(columnValue != null ) ? formatTheDateToHourMinute(columnValue) : ''}}
                                        </td>
                                    </template>
                                    <template v-else-if="dateTimeFormat.includes(column)">
                                        <td class="p-1 text-left font-semibold"  
                                        :class="{ 'text-center': textCenterColumns.includes(column) }"
                                        v-if="response.displayable.includes(column)"> 
                                            {{(columnValue != null ) ? formatTheDateToDateTime(columnValue) : ''}}
                                        </td>
                                    </template>

                                    <!-- {{clickDeliveryDetailId}} -->
                                    <template v-else-if="column=='route'">
                                    <td class="w-40 p-1 text-left font-semibold"  
                                        :class="{ 'text-center': textCenterColumns.includes(column) }"
                                        v-if="response.displayable.includes(column)"> 
                                            <template  v-if="(record.driver == getAuth.user.name) || getAuth.isFirstLevelUser || getAuth.isSecondLevelUser" >
                                                <a href="#"  @click.prevent="clickDeliveryDetailId=record.id" 
                                                class="font-semibold  text-indigo-500 hover:underline"  
                                                    >                     
                                                <span class="font-medium" >{{columnValue}}</span>
                                                </a> 
                                            </template>
                                            <template v-else>
                                                <span class="font-medium" >{{columnValue}}</span>
                                            </template>
                                           
                                    </td>
                                    </template>

                                    
                                    <template v-else>
                                        <td class="p-1 text-left font-semibold"  
                                        :class="{ 
                                            'text-center': textCenterColumns.includes(column),
                                            'w-12' : dollarsSymbolColumns.includes(column)
                                            }"
                                        v-if="response.displayable.includes(column)"> 
                                            {{(dollarsSymbolColumns.includes(column) && columnValue != null) ?'$' : '' }}{{columnValue}}
                                        </td>
                                    </template>
                                </template>
                            </template> <!-- End Not in Edit Mode -->                               
                               
                        </template> <!-- end Loop through each column of rows-->

                         <!-- Last Column - Actions -->  
                            
                             <td>
                              <div v-if="(record.driver == getAuth.user.name  &&  record.approve == 'No' &&  !editing.id) || (getAuth.isFirstLevelUser &&  !editing.id) ">
                                
                                <a href="#" @click.prevent="edit(record)"  v-if="editing.id !== record.id"
                                class=" mr-1 py-1 px-3 shadow-md rounded-full bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none"
                                >
                                Edit
                                </a>   
                                 <a href="#" 
                                @click.prevent="destroy(record.id)" 
                                v-if="response.allow.deletion && editing.id !== record.id 
                                && canSelectItems" 
                                class=" mr-1 py-1 px-2 shadow-md rounded-full bg-red-400 text-white text-sm hover:bg-red-700 focus:outline-none"
                                >
                                Delete
                                </a>  
                            </div>

                            <div>
                                <template v-if="editing.id === record.id"> 
                                <a href="#" @click.prevent="editing.id = null"  v-if="editing.id === record.id"
                                class="p-1 bg-transparent border border-gray-600  shadow-md rounded-full  text-grey text-sm hover:bg-green-700 hover:text-white focus:outline-none"
                                >
                                Cancel
                                </a>  

                                <a href="#" @click.prevent="update"  v-if="editing.id === record.id"
                                class="m-2 py-2 px-3 shadow-md rounded-full bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none"
                                >
                                Save
                                </a>    
                                </template>
                            </div>
                        </td> 
                        <div v-if="record.id== clickDeliveryDetailId">
                            <Delivery_Detail_Modal  
                            :delivery_JourneyId="record.id" 
                            :theRecord="record" 
                            @close="closeDeliveryDetailModal()" 
                            @refreshRecords="getRecords" 
                            />
                     </div>
                        </tr>
                    </tbody>
                    <p class="mt-2 text-red-600 text-center text-sm" >Count : {{filteredRecords.length}}</p>
                </table>
            </div>
            <p v-else>No results</p>
        </div>
    </div>
</div>
    
</template>



<script>
import moment from 'moment'
import Loading from 'vue-loading-overlay';
import Delivery_Detail_Modal from './delivery_modal/delivery_detail_modal.vue'
import { mapGetters, mapState } from 'vuex'
import queryString from 'query-string' //use package query-string npm install query-string

export default {
  middleware: [
        //   redirectIfNotCustomer
      ],
    props: [],    
    components: {Loading,Delivery_Detail_Modal},
   data() {
            return {
                response: {
                    table: '',
                    displayable: [],
                    records: [],
                    allow: {},
                },
                sort: {
                    key: 'id',
                    order: 'desc'
                },
                creating: {
                    active: false,
                    form: {
                        departure: this.getTodayDateAndTime(),
                        date: new Date().toISOString().substr(0, 10), // 05/09/2019
                    },
                    errors: [],
                },
                editing: {
                    id: null,
                    form: {},
                    errors: []
                },

                apartment: {
                    price: '',
                    rooms: ''
                },
                apartments: [],

                counter: 1,
                inputs: [{
                    id: 'fruit0',
                    label: 'Enter Fruit Name',
                    value: '',
                }],

                delivery_details: [{
                    index: 0,
                    id: 'Destination_1',
                    order_number: '',
                    zone: 'YAR',
                    cash_received: 0,
                    change: 0,
                }],
                

              
                
                
                isLoading: false,
                fullPage: true,

                selected: [],

                  // Hide Column Section : checkbox of columns that completely disappear
                unshownColumns:[''],

                // columns hidden - can be show by unclick the radio buttons
                hideColumns:['fuel_payment','approve'],
                // columns unshown in edit mode
                unshownColumnsInEditMode:['date'],

                // columns unshown according to user levels
                secondLevel_ColumnNotAllowsToShow: [
                                    
                ],
 
                thirdLevel_ColumnNotAllowsToShow: [
                    
                ],     
                            
                fourthLevel_ColumnNotAllowsToShow: [
                ],

                // columns does not allow to edit according to user levels
                secondLevel_ColumnNotAllowsToEdit: [
                
                ],                

                thirdLevel_ColumnNotAllowsToEdit: [
                     
                ],     
                            
                fourthLevel_ColumnNotAllowsToEdit: [
                   'driver', 'approve'
                ],

                dateTimeFormat: [],
                dateOnlyFormat: ['date'],
                timeOnlyFormat: ['departure','est_arrival','actual_arrival','est_return','actual_return'],

                
                textCenterColumns:[],
                dollarsSymbolColumns:[],
                
                limit:100,

                // fiter section

                
                selected_driver: 'All',
                selected_approval: 'All',
                quickSearchQuery: '',

                //advanced Dilter section

                  filter: {
                    active: false,
                },
                search:{
                    value: '',
                    operator:'equals',
                    column: 'date',
                    value_1: '',
                    operator_1:'equals',
                    column_1: 'date'
                },

                advancedFilterColumns:['date','approve'],


                // today: new Date().toISOString().substr(0, 10), // 05/09/2019
                todayDateAndTime: this.getTodayDateAndTime(), // 05/09/2019

                selected_dropdown_active: false,

                  //image upload
                image:null,
                imagePreview:null,
                imagePreviewUpdate:null,               
                currentPreviewUpdateId:null,  

                 // showIMGModal: false,
                clickImgSliderModalId : null,

                // showGoodsMaterialsModal: false
                clickDeliveryDetailId : null,

                destinationWidths : [
                ],
               
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

    ...mapState(['sideBarOpen']),
      filteredRecords () {
                // return this.response.records;
                let data = this.response.records;                

                // quick search query
                data = data.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLowerCase().indexOf(this.quickSearchQuery.toLowerCase()) > -1
                    })
                })
                
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
            canSelectItems() {
                return this.filteredRecords.length <= 500
            },
            getRoleNames(){
                const rolNameArray = []
                if (this.response.userRoleOptions){
                    const allRoleNames = this.response.userRoleOptions
                    allRoleNames.forEach(element => {
                    rolNameArray.push(element.name)
                });                }
                
              
                return rolNameArray;

            },
            columnsNotAllowToShowAccordingToUserLevel(){
                if(this.getAuth.isFirstLevelUser) {
                    return [] ;
                } else if (this.getAuth.includesisSecondLevelUser){
                    return this.secondLevel_ColumnNotAllowsToShow;
                } 
                else if (this.getAuth.isThirdLevelUser){
                    return this.thirdLevel_ColumnNotAllowsToShow ;
                } 
                else if (this.getAuth.isFourthLevelUser){
                    return this.fourthLevel_ColumnNotAllowsToShow ;
                } 
                return this.fourthLevel_ColumnNotAllowsToShow;
            },

            columnsNotAllowToEditAccordingToUserLevel(){
                if(this.getAuth.isFirstLevelUser) {
                    return [] ;
                } else if (this.getAuth.includesisSecondLevelUser){
                    return this.secondLevel_ColumnNotAllowsToEdit;
                } 
                else if (this.getAuth.isThirdLevelUser){
                    return this.thirdLevel_ColumnNotAllowsToEdit ;
                } 
                else if (this.getAuth.isFourthLevelUser){
                    return this.fourthLevel_ColumnNotAllowsToEdit ;
                } 
                return this.fourthLevel_ColumnNotAllowsToEdit;
            },

            columnsNotAllowToShowAccordingToUserLevel(){
                if(this.getAuth.isFirstLevelUser) {
                    return [] ;
                } else if (this.getAuth.includesisSecondLevelUser){
                    return this.secondLevel_ColumnNotAllowsToShow;
                } 
                else if (this.getAuth.isThirdLevelUser){
                    return this.thirdLevel_ColumnNotAllowsToShow;
                } 
                else if (this.getAuth.isFourthLevelUser){
                    return this.fourthLevel_ColumnNotAllowsToShow;
                } 
                return this.fourthLevel_ColumnNotAllowsToShow;
            },

             
  },

   methods: {
            toggleNewRecord(){
                this.creating.active = !this.creating.active
                if( this.creating.active){
                    this.todayDateAndTime = this.getTodayDateAndTime();
                }
            },
            closeDeliveryDetailModal(){
                this.clickDeliveryDetailId=null;
                this.getRecords();
            },
            
            getRecords(){
                return axios.get(`/api/datatable/delivery_journeys?${this.getQueryParameters()}`).then((response)=> {
                    this.response = response.data.data;
                })
            },
            getQueryParameters () {
                return queryString.stringify({
                    limit: this.limit,
                    locationId: this.locationId,   
                    driver: this.selected_driver,
                    approve: this.selected_approval,
                    ...this.search
                })
                    
            },
            sortBy(column){
            this.sort.key = column
            this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
            },
            edit (record) {
                this.editing.errors = []
                this.editing.id = record.id
                this.editing.form = _.pick(record, this.response.updatable)
                if(this.editing.form.actual_return){
                    this.editing.form.actual_return = this.formatTheDateToDateTimeLocal(this.editing.form.actual_return)
                } else {
                  
                    this.editing.form.actual_return = this.getTodayDateAndTime();

                }
            },
            isUpdatable (column) {
                return this.response.updatable.includes(column)
            },
            toggleSelectAll () {
                if (this.selected.length == this.filteredRecords.length) {
                    this.selected = []
                    return
                }

                this.selected = _.map(this.filteredRecords, 'id')
            },
            update () {
                this.isLoading = true
                axios.patch(`/api/datatable/delivery_journeys/${this.editing.id}`, this.editing.form).then((response) => {
                    this.isLoading = false
                    this.getRecords().then(() => {
                        this.editing.id = null
                        this.editing.form = null
                       
                      
                    })
                }).catch((error) => {
                    this.isLoading = false
                    if (error.response.status === 422) {                        
                        this.editing.errors = error.response.data.errors
                    }
                })
            },
            store () {    
                this.isLoading = true
                this.creating.form.delivery_details = this.delivery_details
              
                
                axios.post(`/api/datatable/delivery_journeys`, this.creating.form).then((response) => {
                    this.isLoading = false
                    this.getRecords().then(() => {
                        // this.creating.active = true
                        // this.creating.form = {}
                        // this.creating.errors = []
                        // this.creating.form.date =  new Date().toISOString().substr(0, 10)

                         this.delivery_details = [
                            {
                            index: 0,
                            id: 'Destination_1',
                            order_number: '',
                            zone: 'YAR',
                            cash_received: 0,
                            change: 0,
                            }
                        ]

                    })
                }).catch((error) => {
                    this.isLoading = false
                    if (error.response.status === 422) {
                        this.creating.errors = error.response.data.errors
                        
                    }
                })
            },

          
            exportPDF(){
                axios.get(`/api/datatable/delivery_journeys/createPDF`,
                      {responseType: 'arraybuffer'}
                )
                
                .then((response)=>{
                     //code to download file after generating
                    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    var fileLink = document.createElement('a');
                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'goods.pdf');
                    document.body.appendChild(fileLink);
                    fileLink.click();
                    console.log(fileLink);
                }).catch((error) => {
                    this.isLoading = false
                    if (error.response.status === 422) {
                        this.creating.errors = error.response.data.errors                       
                    }

                })
            },
               
            destroy(record){
                if(!window.confirm(`Are you sure?`)){
                    return
                }
                this.isLoading = true

                axios.delete(`/api/datatable/delivery_journeys/${record}`).then(()=>{
                    this.isLoading = false
                    this.selected= [],
                    this.selected_dropdown_active = false,
                    this.getRecords()
                })
                
            },

            sumFuel(ids){
                // console.log(ids);
                const idsArray =Object.values(ids)
                // const myArrayId = ids.split(",")
                // alert(myArrayId);
                // console.log(idsArray);
                let sum = 0
                
                for(let i=0; i< idsArray.length; i++){
                    for(let j=0; j< this.filteredRecords.length; j++){                     
                        if(idsArray[i] == this.filteredRecords[j].id){
                            if(this.filteredRecords[j].approve == 'No'){
                                if(!window.confirm('Journey having Id ' + this.filteredRecords[j].id + ' does not get approval yes. You want to continue anyway.'))
                                    {
                                    return
                                    }                        
                            }
                            sum = sum + this.filteredRecords[j].fuel_payment
                            break
                        }
                    }
                }
                alert('Fuel Payment is:  $' + sum);
                // ids.forEach(selectedId => {
                //     this.filteredRecords.forEach(recordId => {
                //         if(selectedId == recordId)
                //         sum = sum + element.fuel
                //     });
                //     console.log(element)
                // });
                // console.log(this.filteredRecords);
                
            // this.isLoading = true

            // axios.post(`/api/datatable/invoice_from_supplier_line/addAmountFromInvoiceToStock/${record}`).then((response)=>{
                // this.isLoading = false
            //   if (response.data == 'Added Successfully'){
            //       window.alert('Added sucessfully from invoice to your stock !')
            //   } else {
            //     let nameOfGM_CannotBeUpdated = [];
            //     response.data.forEach(element => {
            //        nameOfGM_CannotBeUpdated.push('\n ***' + element.goods_material)
            //     });

            //     alert('To be added or deducted the quantity from the invoice to your stock, name and unit of Good and Material in Table Good Material must be exactly the same as in Invoices_From_Suppliers. \n  Good Materials below can not be added  \n Please re-check the name or unit of good material. \n' 
            //     + nameOfGM_CannotBeUpdated.toString() )


            //   }
            //     this.getRecords()
            //     })
            },
            imageSelected(e) {
                this.image = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(this.image);
                reader.onload = e => {
                    this.imagePreview = e.target.result;
                }
            },
            imageChanged(e,id) {
                this.image = e.target.files[0];
                let reader = new FileReader();
                reader.readAsDataURL(this.image);
                reader.onload = e => {
                    this.imagePreviewUpdate = e.target.result;
                    this.currentPreviewUpdateId= id;
                }
            },

            saveImage(productId) {
                            
                if(this.image){
                    let data = new FormData
                    data.append('image', this.image)
                this.isLoading = true
                axios.post(`/api/datatable/delivery_journeys/saveImage/${productId}`, data).then((response)=>{
                    this.isLoading = false
                    // window.location.reload(false);
                    this.getRecords().then(() => {
                        this.currentPreviewUpdateId = null;
                        this.imagePreviewUpdate = null;
                        this.image =null;
                    })
                
                }).catch((error) => {
                    if (error.response.status === 422) {
                        this.creating.errors = error.response.data.errors                       
                    }
                })
                }  
            },

            // openImageModal(goods_material_id){
            //     this.clickThumbnailId = goods_material_id;
            // },

            openSliderImageModal(record) {        
                this.clickImgSliderModalId = record.id;
            },

           
            addDestination(){
                this.delivery_details.push(  
                    {
                        index: this.counter,
                        id: `Destination_${++this.counter}`,
                        order_number: '',
                        zone: 'YAR',
                        cash_received: 0,
                        change: 0,
                    });
            },
            removeDestination(index){
              
                if (index > -1) {
                    this.counter = this.counter - 1
                   
                    this.delivery_details.splice(index, 1)
                         // reassing index and id of element in delivery_details -- start from index
                   for (let arrayIndex = index; arrayIndex < this.delivery_details.length; arrayIndex++) {
                       const the_delivery_detail= this.delivery_details[arrayIndex];
                       console.log(the_delivery_detail)
                       the_delivery_detail.index =arrayIndex
                       let id = arrayIndex + 1
                       the_delivery_detail.id =`Destination_${id}`
                   }
                       
                      
                   

                  
                }
                // this.delivery_details.push(  
                //     {
                //         id: `Destination_${++this.counter}`,
                //         order_number: '',
                //         zone: 'YAR',
                //         cash_received: 0,
                //         change: 0,
                //     });
            },
            addInput() {
                this.inputs.push({
                    id: `fruit${++this.counter}`,
                    label: 'Enter Fruit Name',
                    value: '',
                });
            },

            getTodayDateAndTime(){
                var today = new Date();
                var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
                var currentHours=today.getHours();
                var currentMinutes=today.getMinutes();
                var hourInTwoMinutes = ("0"+currentHours).slice(-2)
                var minutesInTwoMinutes = ("0"+currentMinutes).slice(-2)
                var time = hourInTwoMinutes + ":" + minutesInTwoMinutes
                var dateTime = date + 'T' + time              
                return dateTime;
            },

            formatTheDateToDateTimeLocal(rawDate){
                return moment(String(rawDate)).format('YYYY-MM-DDThh:mm')
            },

            formatTheDateToHourMinute(rawDate){
                return moment(String(rawDate)).format('hh:mm A')
            },
            formatTheDate(rawDate){
                return moment(String(rawDate)).format('DD/MM/YYYY')
            },
            formatTheDateToDateTime(rawDate){
                return moment(String(rawDate)).format('DD/MM/YYYY hh:mm A')
            },
            getWidth(){
                var a = 1/2
                return a
            },



            
        },

        mounted() {
            this.getRecords()
        },
    
}

</script>

<style  lang="scss">
    .sortable {
        cursor: pointer;
    }

    .arrow {
        display: inline-block;
        vertical-align: middle;
        width: 0;
        height: 0;
        margin-left: 5px;
        opacity: .6;

        &--asc {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-bottom: 5px solid #222;

        }

         &--desc {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #222;

        }
    }

  

</style>