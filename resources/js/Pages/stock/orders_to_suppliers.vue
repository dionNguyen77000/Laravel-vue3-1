<template>

  <div id="orders_to_suppliers" class="p-6">     
        <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center rounded-lg shadow-md">
             <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :is-full-page="fullPage"/>
            <div class="w-full p-1">
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Orders To Suppliers</div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    v-if="response.allow.creation && isFirstLevelUser"
                    @click.prevent="clickGoodsMaterialId=true">
                        New Order
                    </a>
                </div>              
            </div>

          <!-- show hide column section -->
        <div id="show_hide_section" class="text-center mx-4 space-y-2">
            <p> <b>Hide Column </b></p>
            <ul id="hide_show_column_section" class="width-3/4 flex flex-wrap justify-center">
                <li  class="mr-3" v-for="column in response.displayable" :key="column"
                :class="{ 'hidden': hiddenSelectedColumns.includes(column)}"
                >
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
        class="p-2 m-1 inline-block text-blue-300 transition duration-300  hover:text-blue-500 focus:outline-none"
        v-if="response.allow.creation && isFirstLevelUser"
        @click.prevent="filter.active = !filter.active">
        {{ filter.active ? 'Hide Filter' : 'Advanced Filter' }}
        </a>      
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
                   <input v-if="search.column=='ordered_date'"
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
                    
                    <input v-if="search.column_1=='ordered_date'"
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
                        </ul>
                    </div>

            
                <div class="relative">
                    <select v-model = "limit" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option value="2">2</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">200</option>
                        <option value="">All</option>
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
            <div class="relative">
                <select v-model="selected_supplier" @change="getRecords"
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option  value= '' >Supplier</option>
                        
                    <option v-for="option,index in response.supplierOptions" :value="index" :key="option">
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
            
            </div>

             <div class="flex flex-row mb-1 sm:mb-0">          
                <div class="relative">
                    <select v-model="selected_user" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option  value= ''>Staff</option>         
                        <option v-for="option,index in response.userOptions" :value="index" :key="option">
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
      <!-- end filter section -->

        <!-- start Table -->        
        <div  v-if="filteredRecords.length" class="bg-white shadow-md rounded my-3  overflow-x-auto">
            <table class="min-w-max w-full table-auto">
                <!-- Table Heading Section -->
                <thead>
                    <tr class="collapse py-2 bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th 
                        v-if="isFirstLevelUser && canSelectItems"
                        class="py-2"
                        >
                                <input type="checkbox" 
                                @change="toggleSelectAll" 
                                :checked="filteredRecords.length === selected.length"
                                >
                        </th>
                        <template v-for="column in response.displayable" :key="column">
                            <!-- Table Heading - Edit Mode-->
                             <template v-if="editing.id">
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
                                :class="{ 'text-center': textCenterColumns.includes(column) }"
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
                        <th class="text-left">Actions</th>                       
                    </tr>
                </thead>
                <!-- End Table Heading -->
                <!-- Row (Records) Section -->                
                <tbody class="text-gray-600 text-sm font-light">  
                    <!-- Loop Through each records getting from controller -->
                    <tr v-for="record in filteredRecords" :key="record"  class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100"
                    :class="{ 'bg-red-500 text-white hover:bg-red-600' : record.Preparation==('Yes')}" 
                    >                      
                        <td v-if="isFirstLevelUser && canSelectItems" class=" text-center">
                            <input type="checkbox" :value="record.id" v-model="selected">
                        </td>
                        <!-- Loop through each column-->
                        <template v-for="columnValue,column in record" :key="column">
                        <!-- Edit Mode-->
                        <template v-if="editing.id">
                            <!-- Edit Mode - column not show -->
                            <template v-if="unshownColumnsInEditMode.includes(column)
                                    || hideColumns.includes(column) || !isUpdatable(column)">
                            </template>                        
                            <!-- if the record currently edit --> 
                            <template v-else-if="editing.id === record.id">
                                <td class="py-2 text-left"  
                                v-if="response.displayable.includes(column)">   
                                <template v-if="column=='supplier'">                                  
                                <select 
                                class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                :class="{
                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)
                                }"
                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                :name="column" :id="column" 
                                v-model="editing.form[column]">                                    >
                                    <template v-for="option,index in response.supplierOptions" >   
                                        <template v-if="record.id != option.id">                  
                                        <option :value="index" :key="option">
                                            {{option}} 
                                        </option>
                                        </template>
                                    </template>                                       
                                </select>
                                </template>   
                                <template v-else-if="column=='user'">
                                <!-- {{record}} -->
                                    <select
                                    class='rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    :class="{
                                        'bg-pink-200' : !isFirstLevelUser
                                    }"
                                    :name="column" :id="column" 
                                    v-model="editing.form[column]"
                                    
                                    :disabled= "!isFirstLevelUser" 
                                    > 
                                            <!-- <template v-if=" record.date+' '+record.user_id+' '+record.intermediate_product_id != option.id">                   -->
                                            <option v-for="option,index in response.userOptions"  :value="index" :key="option">
                                            {{option}} 
                                            </option>
                                            <!-- </template> -->
                                    </select>                                  
                                </template> 
                                <template v-else-if="column=='estimated_price'">
                                    $<input type="text"  
                                    class='w-3/4 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    v-model="editing.form[column]"                              
                                    disabled>                                   
                                </template>                               
                                <template v-else>
                                <input type="text"  
                                    class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                v-model="editing.form[column]"
                                :class="{ 
                                'border-3 border-red-700': editing.errors[column] ,
                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)}"
                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                > 
                                <br>
                                <span v-if="editing.errors[column]" class="text-white font-bold">
                                    <strong>{{ editing.errors[column][0] }}</strong>
                                </span>
                            </template> 
                                </td>                               
                            </template>
                            <!-- Edit Mode - for rows not currently edit -->
                            <template v-else>
                                <td class="py-2 text-left"  
                                :class="{ 'text-center': textCenterColumns.includes(column) }"
                                v-if="response.displayable.includes(column)">   
                                <div class="items-center">

                                    <template v-if="column=='supplier_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >{{response.supplierOptions[columnValue]}}</span>
                                        </div>
                                    </template>

                                    <template v-else-if="column=='user_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >
                                            {{columnValue}}  - {{response.userOptions[columnValue]}}
                                            </span>
                                        </div>
                                    </template>

                                    <template v-else-if="column=='ordered_date'">                                 
                                            <div class="w-20">
                                                <span class="font-medium">
                                                    {{columnValue}}
                                                    </span>
                                            </div>
                                    </template>

                                    <template v-else-if="column=='excel_file'">
                                            <a v-bind:href="columnValue">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path></svg>                                        
                                            </a>
                                    </template>

                                    <template v-else-if="column=='Note'">

                                            <a  @click.prevent="clickNoteModalId = record.id">
                                                
                                                <svg v-if="columnValue" class="w-6 h-6 text-red-500 hover:text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>                                     
                                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>                                        
                                                </a>

                                    </template>


                                    <template v-else>
                                        <span  class="font-medium" >
                                        {{(dollarsSymbolColumns.includes(column) && columnValue != null) ?'$' : '' }}{{columnValue}}
                                        </span>
                                    </template>
                                    </div>
                                </td>             
                            </template> <!-- End Edit Mode - the rows current not edit -->                    
                        </template>    <!--End Edit Mode-->
                        
                        <template v-else>  <!-- Not in Edit Mode-->
                            <template v-if="hideColumns.includes(column)">
                            </template>
                            <template v-else>
                                <td class="py-2 text-left"  
                                :class="{ 'text-center': textCenterColumns.includes(column) }"
                                v-if="response.displayable.includes(column)">   
                                <div class="items-center">

                                    <template v-if="column=='supplier_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >{{response.supplierOptions[columnValue]}}</span>
                                        </div>
                                    </template>

                                    <template v-else-if="column=='user_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >
                                            {{columnValue}}  - {{response.userOptions[columnValue]}}
                                            </span>
                                        </div>
                                    </template>

                                    <template v-else-if="column=='ordered_date'">                                 
                                            <div class="w-20">
                                                <span class="font-medium">
                                                    {{columnValue}}
                                                    </span>
                                            </div>
                                    </template>

                                    <template v-else-if="column=='excel_file'">
                                            <a v-bind:href="columnValue">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path></svg>                                        
                                            </a>
                                    </template>

                                    <template v-else-if="column=='Note'">

                                            <a  @click.prevent="clickNoteModalId = record.id">
                                                
                                                <svg v-if="columnValue" class="w-6 h-6 text-red-500 hover:text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>                                     
                                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>                                        
                                                </a>

                                    </template>


                                    <template v-else>
                                        <span  class="font-medium" >
                                        {{(dollarsSymbolColumns.includes(column) && columnValue != null) ?'$' : '' }}{{columnValue}}
                                        </span>
                                    </template>
                                    </div>
                                </td>  
                            </template>
                        </template> <!-- End Not in Edit Mode -->                            
                    </template><!-- end Loop through each column of rows-->
                    <!-- Last Column - Actions -->
                    <td>
                    <div>
                        <a href="#" @click.prevent="edit(record)"  v-if="editing.id !== record.id"
                        class=" mr-1 py-1 px-3 shadow-md rounded-full bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none"
                        >
                        Edit
                        </a>   

                        <a href="#" @click.prevent="destroy(record.id)" v-if="response.allow.deletion && editing.id !== record.id" 
                        class=" mr-1 py-1 px-2 shadow-md rounded-full bg-red-400 text-white text-sm hover:bg-red-700 focus:outline-none"
                        >
                        Delete
                        </a>  

                            <a href="#"  @click.prevent="view(record)"  v-if="editing.id !== record.id && editing.id == null"
                        class=" mr-1 py-1 px-3 shadow-md rounded-full bg-green-500 text-white text-sm hover:bg-yellow-700 focus:outline-none"
                        >
                        View
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
                    <div v-if="record.id== clickNoteModalId">
                        <Note_Modal  
                            :note="record.Note"
                            :id="record.id" 
                            :table_name="'orders_to_supplier'"
                            @close="clickNoteModalId=null" 
                            @refreshRecords="getRecords" 
                        />
                    </div>
                    
                    <div v-if="record.id== clickThumbnailId">
                        <Orders_To_Suppliers_Line_Modal  
                        :order_total_price="order_to_supplier_line_estimated_price"
                        :order_to_supplierId="record.id" 
                        @close="clickThumbnailId=null" 
                        />
                    </div>
                    </tr>
                </tbody>
            </table>
        </div>
        <p v-else class="mt-2 text-red-600 text-center text-lg">No Data</p>
        </div>
    </div>
    
     <div v-if="clickGoodsMaterialId==true">
        <Goods_Material_Modal  
        @close="clickGoodsMaterialId=null" 
        />
    </div>

   
    </div>
    
</template>



<script>
import Modal from  '../../components/modal.vue'
import Note_Modal from  './stock_modal/note_modal.vue'
import Orders_To_Suppliers_Line_Modal from './stock_modal/orders_to_suppliers_line_modal.vue'
import Goods_Material_Modal from './stock_modal/goods_material_modal.vue'
import {mapGetters, mapState } from 'vuex'
import queryString from 'query-string' //use package query-string npm install query-string
export default {

    middleware: [
        //   redirectIfNotCustomer
      ],

    components: {Modal,Note_Modal,Orders_To_Suppliers_Line_Modal,Goods_Material_Modal},
    props: ['orders_to_supplierId'],    
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
                        Active: 1,
                        permission_id: 1,
                        supplier_id:1,
                        unit_id:1,
                        category_id:1
                    },
                    errors: [],
                },
                editing: {
                    id: null,
                    form: {},
                    errors: []
                },
                filter: {
                    active: false,
                },
                search:{
                    value: '',                                                                                      
                    operator:'equals',
                    column: 'ordered_date',
                    value_1: '',                                                                                      
                    operator_1:'equals',
                    column_1: 'ordered_date'
                },
                
                
                selected: [],
                // hideColumns:['slug','description','image'],
                hideColumns:['invoices_from_supplier'],
                hiddenSelectedColumns:['invoices_from_supplier'],
                textCenterColumns:['estimated_price'],
                unshownColumnsInEditMode: ['excel_file','Note'],
                notAllowEditExceptPeopleInCharge: [],
                dollarsSymbolColumns:['estimated_price'],
                advancedFilterColumns:['id','user','ordered_date','estimated_price'],
                

                limit:50,
                quickSearchQuery: '',
                selected_supplier: '',
                selected_user: '',

                selected_dropdown_active: false,
                 // showModal: false,
                clickThumbnailId : null,
                // showGoodsMaterialsModal: false
                clickGoodsMaterialId : null,
                // show Notes Modal: false
                clickNoteModalId : null,

                isLoading: false,

                // Level of Users
                firstLevelUsers : ['Admin' , 'Manager'],
                secondLevelUsers : [],

                order_to_supplier_line_estimated_price: null,
                
               
            }
        },
       
 computed: {
    ...mapGetters({
            getAuth: 'auth/getAuth'
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
                    // console.log("🚀 ~ file: DataTable.vue ~ line 53 ~ data=_.orderBy ~ i", i)
                        
                        let value = i[this.sort.key]
                        // console.log("🚀 ~ file: DataTable.vue ~ line 54 ~ data=_.orderBy ~ value", value)
                        
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
                const rolNameArray = [];
                const allRoleNames = this.getAuth.user.roles;
                allRoleNames.forEach(element => {
                    rolNameArray.push(element.name)
                });
              
                return rolNameArray;

            },
             notAllowToEditExceptPeopleInCharge(){
                if(this.getRoleNames.includes('Admin') || this.getRoleNames.includes('Manager')) {
                    this.notAllowEditExceptPeopleInCharge = []
                }
                return this.notAllowEditExceptPeopleInCharge
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

methods: 
{
            
    getRecords(){
        return axios.get(`/api/datatable/orders_to_supplier?${this.getQueryParameters()}`).then((response)=> {
            this.response = response.data.data;
            console.log("🚀 ~ file: orders_to_suppliers.vue ~ line 336 ~ returnaxios.get ~  this.response",  this.response)
        })
    },
    getQueryParameters () {
        return queryString.stringify({
            limit: this.limit,
            supplier: this.selected_supplier,
            user: this.selected_user,
            permission_id: this.selected_permission,
            orders_to_supplierId: this.orders_to_supplierId,            
            ...this.search
        }, 
        {
            skipNull: true
        }
        )
            
    },
    sortBy(column){
    this.sort.key = column
    this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
    },
    edit (record) {
        this.editing.errors = []
        this.editing.id = record.id
        this.editing.form = _.pick(record, this.response.updatable)
    },
     editCurrentQty(record){
        
        this.editing.errors = []
        this.editing.currentQtyId = record.id
        this.editing.form = _.pick(record, this.response.updatable)
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
   update() {

         axios.patch(`/api/datatable/orders_to_supplier/${this.editing.id}`, this.editing.form).then((response) => {
                this.getRecords().then(() => {
                    this.editing.id = null
                    this.editing.form = null
                    if(response.data=='password updated') {
                        alert('Password updated successfully !')
                    }
                    
                })
            }).catch((error) => {
                if (error.response.status === 422) {                        
                    this.editing.errors = error.response.data.errors
                    console.log("🚀 ~ file: DataTable.vue ~ line 262 ~ axios.patch ~ this.editing.errors", this.editing.errors)
                    console.log("🚀 ~ file: DataTable.vue ~ line 262 ~ axios.patch ~ error.response.data.errors", error.response.data.errors)
                }
            })
    },
    store () {
        if(this.image){
            let data = new FormData
            data.append('image', this.image)
            this.creating.form.image = data
        }  
        // console.log('submiss form is : ', this.creating.form)

        axios.post(`/api/datatable/orders_to_suppliers`, this.creating.form).then((response) => {
        // console.log("🚀 ~ file: DataTable.vue ~ line 238 ~ axios.post ~ this.endpoint", this.endpoint
           if(response.data.id && this.image) {
                let data = new FormData;
                data.append('image', this.image)
            
            axios.post(`/api/datatable/orders_to_suppliers/saveImage/${response.data.id}`, data).then((response1)=>{
                this.getRecords().then(() => {
                    console.log('save Image sucessfully')
                    this.imagePreview = null;
                    this.image =null;
                })
            }).catch((error) => {
                if (error.response.status === 422) {
                    this.creating.errors = error.response.data.errors                       
                }
            })
            } else {
                alert ('unsucessfully save recores')
            }

            this.getRecords().then(() => {
                this.creating.active = true
                this.creating.form = {}
                this.creating.errors = []
            })
            if(response.data) {
                console.log('saved successfully !')
            } else {
                alert ('unsucessfully save image! please contact Admin')
            }
        }).catch((error) => {
            if (error.response.status === 422) {
                this.creating.errors = error.response.data.errors                       
            }
        })
    },
        
    destroy(record){
    // console.log("🚀 ~ file: DataTable.vue ~ line 174 ~ destroy ~ record", record)

        if(!window.confirm(`Are you sure?`)){
            return
        }

        axios.delete(`/api/datatable/orders_to_supplier/${record}`).then(()=>{
            this.selected= [],
            this.selected_dropdown_active = false,
            this.getRecords()
        })
        
    },

    view(record) {
        this.clickThumbnailId = record.id;
        let order_to_supplier_line = _.find(this.response.records, ['id', record.id]);
        this.order_to_supplier_line_estimated_price = order_to_supplier_line['estimated_price']
    },

   

    imageSelected(e) {
        // this.creating.form.imageIcon = e.target.files[0];
        this.image = e.target.files[0];
        let reader = new FileReader();
        // reader.readAsDataURL( this.creating.form.imageIcon);
        reader.readAsDataURL(this.image);

        reader.onload = e => {
            this.imagePreview = e.target.result;
        }
    },
    imageChanged(e,id) {
        console.log('hei, id of the record is', id);
        // this.creating.form.imageIcon = e.target.files[0];
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
        
        console.log(data)

        axios.post(`/api/datatable/orders_to_suppliers/saveImage/${productId}`, data).then((response1)=>{
            this.getRecords().then(() => {
                console.log('save Image sucessfully')
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


  

  
    importExcel(){
        axios.post(`/api/datatable/orders_to_suppliers/fileImport`).then(()=>{
            
        })
        
    },
    exportExcel(){
        axios.get(`/api/datatable/orders_to_suppliers/fileExport`).then(()=>{
            console.log('work')
        }).catch((error) => {
            console.log(error)
        })
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