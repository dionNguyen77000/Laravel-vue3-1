<template>
      
  <div id="miscellaneous_invoices" class="p-6"> 
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :is-full-page="fullPage"/>
        <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center rounded-lg shadow-md">
            <div class="w-full p-1">
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Miscellaneous Invoices</div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    v-if="response.allow.creation && (getAuth.isFirstLevelUser || getAuth.isSecondLevelUser)"
                    @click.prevent="creating.active = !creating.active">
                    {{ creating.active ? 'Hide' : 'New record' }}
                    </a>
                </div>
                
            </div>
          
            <div class="flex justify-center" v-if="response.allow.creation && creating.active">
                <div class="w-10/12 md:w-8/12 lg:6/12 p-6 rounded-lg">
              
                <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">New {{response.table}}</h3>
                  <form action="#" @submit.prevent="store" enctype="multipart/form-data">
                        <!-- @csrf -->
                        <div class="mb-2" v-for="column in response.created" :key="column" >  
                            <template v-if="column=='user'">
                                <label :for="column"  class="font-semibold">Orderer : </label>
                                {{getAuth.user.name}}
                                <!-- <select
                                    class='rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'                              
                                    :name="column" :id="column" 
                                    v-model="creating.form[column]"                                  
                                > 
                                    <option v-for="option,index in response.userOptions"  :value="index" :key="option">
                                        {{option}} 
                                    </option>
                                </select>                                   -->
                            </template> 

                            <template v-else-if="column=='img_thumbnail'">
                                <!-- Photo File Input -->
                                <label  class="font-semibold" for="product_photo">
                                    Product Photo<span class="text-red-600"> </span>
                                </label>
                                 <!-- Current Profile Photo -->
                                <span class="mt-2" x-show="imagePreview">
                                    <img :src="imagePreview" class="w-20  shadow">
                                </span>
                                <div >
                                   <input type="file" @change="imageSelected" id="product_photo">
                                </div>                       
                            </template>  

                             <template v-else-if="column=='supplier'">
                            <label :for="column"  class="font-semibold">Supplier : </label>
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option :value="option" v-for="option,index in response.supplierOptions" :key="option">
                                    {{option}}
                                </option>
                            </select>
                            </template>    

                            <template v-else-if="column=='received_date'">
                                <label :for="column"  class="font-semibold">Date : </label>
       
                                <input v-if="search.column=='received_date'"
                                type="date" 
                                class="appearance-none h-full rounded-l border block appearance-none w-1/2 bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                v-model="creating.form[column]" 
                                >
                            </template>
                                              
                            <template v-else-if="column=='paid'">
                            <label  class="font-semibold" :for="column">Paid : </label>    
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option  value="Yes">Yes</option>
                                <option  value="No">No</option>                              
                            </select>
                            </template> 
                                  
                            <template v-else-if="column=='invoice_category'">
                                <label  class="font-semibold" :for="column">Invoice Category : </label>                           
                                <select :name="column" :id="column" v-model="creating.form[column]">
                                    <option :value="option" v-for="option,index in invoiceCategories" :key="option">
                                        {{option}}
                                    </option>
                                </select>
                            </template> 
                            <template v-else-if="column=='invoice_type'">
                                <label  class="font-semibold" :for="column">Invoice Type : </label>                           
                                <select :name="column" :id="column" v-model="creating.form[column]">
                                    <option :value="option" v-for="option,index in invoiceTypes" :key="option">
                                        {{option}}
                                    </option>
                                </select>
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
                    </form>
                
                </div>
            </div>

        
          <!-- show hide column section -->
        <div id="show_hide_section" class="text-center mx-4 space-y-2">
            <p> <b>Hide Column </b></p>
            <ul id="hide_show_column_section" class="width-3/4 flex flex-wrap justify-center">
                <li  class="mr-3" v-for="column in response.displayable" :key="column"
                :class="{ 
                    'hidden': unshownColumns.includes(column) ||  columnsNotAllowToShowAccordingToUserLevel.includes(column),
                    }"
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
                   <input v-if="search.column=='received_date'"
                    type="date" 
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
                    
                    <input v-if="search.column_1=='received_date'"
                    type="date" 
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
                        <option value="20">20</option>
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
       <!-- {{editing.form}} -->
        <!-- start Table -->        
        <div  v-if="filteredRecords.length" class="bg-white shadow-md rounded my-3  overflow-x-auto">
            <table class="min-w-max w-full table-auto">
                 <!-- Table Heading Section -->
                <thead>
                    <tr class="collapse py-2 bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th 
                        v-if="(getAuth.isFirstLevelUser || getAuth.isSecondLevelUser ||getAuth.isThirdLevelUser) && canSelectItems"
                        class="p-1"
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
                                    <th class="text-left p-1"
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
                                class="text-left p-1" 
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
                        <td v-if="(getAuth.isFirstLevelUser || getAuth.isSecondLevelUser ||getAuth.isThirdLevelUser ||getAuth.isThirdLevelUser) && canSelectItems" class=" text-center">
                            <input type="checkbox" :value="record.id" v-model="selected">
                        </td>
                        <!-- Loop through each column-->
                        <template v-for="columnValue,column in record" :key="column">
                         <!-- Columns not allow to show in any mode-->
                            <template v-if="unshownColumns.includes(column)">
                            </template>
                            <!-- Columns not allow to show in according to level of user-->                           
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
                                <td class="py-2 text-left"  
                                :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                v-if="response.displayable.includes(column)">   
                                 <template v-if="column=='supplier'">
                                    
                                    <select 
                                    class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    :class="{
                                        'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column)
                                    }"
                                    :disabled= " columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
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
                                 <template v-else-if="column=='received_date'"> 
                                    <input type="date" 
                                    :name="column" :id="column"
                                    class="bg-gray-100 border-2 w-52 p-1 rounded-lg"
                                    v-model="editing.form[column]" 
                                    >

                                     <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                        <strong>{{ editing.errors[column][0] }}</strong>
                                    </span>                                   
                                </template> 
                                 <template v-else-if="column=='user'">
                                    <!-- {{record}} -->
                                    <select
                                    class='rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    
                                    :name="column" :id="column" 
                                    v-model="editing.form[column]"                                  
                                    > 
                                        <option v-for="option,index in response.userOptions"  :value="index" :key="option">
                                            {{option}} 
                                        </option>
                               </select>                                  
                                </template> 
                                 <template v-else-if="column=='paid'">
                                    <select
                                    class='rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    :class="{
                                        'bg-pink-200' : !(getAuth.isFirstLevelUser || getAuth.isSecondLevelUser ||getAuth.isThirdLevelUser)
                                    }"
                                    :name="column" :id="column" 
                                    v-model="editing.form[column]"                                  
                                    :disabled= "!(getAuth.isFirstLevelUser || getAuth.isSecondLevelUser ||getAuth.isThirdLevelUser)" 
                                    > 
                                            <!-- <template v-if=" record.date+' '+record.user_id+' '+record.intermediate_product_id != option.id">                   -->
                                            <option value="No">
                                              No
                                            </option>
                                            <option value="Yes">
                                              Yes
                                            </option>
                                            <!-- </template> -->
                               </select>                                  
                                </template> 

                                       
                            <template v-else-if="column=='invoice_category'">
                                <select :name="column" :id="column" v-model="editing.form[column]">
                                    <option :value="option" v-for="option,index in invoiceCategories" :key="option">
                                        {{option}}
                                    </option>
                                </select>
                            </template> 

                            <template v-else-if="column=='invoice_type'">
                                <select :name="column" :id="column" v-model="editing.form[column]">
                                    <option :value="option" v-for="option,index in invoiceTypes" :key="option">
                                        {{option}}
                                    </option>
                                </select>
                            </template> 

                            

                                <template v-else>
                                    <span>
                                    {{dollarsSymbolColumns.includes(column) ?'$' : '' }} 
                                    </span>
                                    <input type="text"  
                                     class='w-20 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                    v-model="editing.form[column]"
                                    :class="{ 
                                    'border-3 border-red-700': editing.errors[column] ,
                                     'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column)}"
                                    :disabled= " columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
                           > 
                                    <br>
                                    <span v-if="editing.errors[column]" class="text-red-700 font-bold">
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
                               

                                 <template v-if="column=='orders_to_supplier_id'">
                                      <a href="#"  @click.prevent="openOrderToSuppliersModal(record)" 
                                       class="font-semibold  text-indigo-500 hover:underline"  
                                        >
                                        {{columnValue}}
                                    </a> 
                                </template>

                            

                                  <template v-else-if="column=='received_date'">                                 
                                         <div class="w-20">
                                             <span class="font-medium">
                                                 {{formatTheDate(columnValue)}}
                                                </span>
                                         </div>
                                </template>

                                <template v-else-if="column=='supplier_id'">
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

                                                 
                                <template v-else>
                                    <span  class="font-medium" >
                                    {{(dollarsSymbolColumns.includes(column) && columnValue != null) ?'$' : '' }}{{columnValue}}
                                    </span>
                                </template>

                                </td>
                             </template>  <!-- End Edit Mode - the rows current not edit -->         
                        </template>  <!--End Edit Mode-->
                       <template v-else>  <!-- Not in Edit Mode-->
                            <template v-if="hideColumns.includes(column)">
                            </template>
                            <template v-else>
                                <td class="py-2 text-left"  
                                :class="{ 'text-center': textCenterColumns.includes(column) }"
                                v-if="response.displayable.includes(column)"> 
                                <div class="items-center">
                                 <!-- Invoice Image -->
                                <template v-if="column=='img_thumbnail'">

                                    <span  :id="'previewImageUpdate_' + record.id" class="mt-2" x-show="!imagePreviewUpdate">
                                        <template v-if="imagePreviewUpdate && record.id == currentPreviewUpdateId" >
                                            <img :src="imagePreviewUpdate" class="w-14 h-14 shadow">
                                        </template>
                                        <template v-else>
                                            <img @click="openSliderImageModal(record)" :src="columnValue" class="w-14 h-14 shadow">
                                        </template>                                      
                                    </span>
                                
                                
                                    <div class="mt-1">
                                        <button  class=" px-1 shadow-md  bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none">
                                            <input type="file" @change="imageChanged($event,record.id)"  
                                            :data-index="record.id"
                                        :id="'changeImage_' + record.id"
                                            accept=".jpg,.jpeg,.png"
                                            class="hidden"/>
                                            <label :for="'changeImage_' + record.id">Change</label>
                                        </button>
                                    </div>
                                    <div class="mt-1 pl-2">
                                        <button v-if="record.id == currentPreviewUpdateId" class="px-3 shadow-md bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none"> 
                                            <input type="submit" @click="saveImage(record.id)"  :id="'saveUpdateImage_' + record.id" class="hidden"/>
                                        <label :for="'saveUpdateImage_' + record.id">Save</label>
                                        </button>   
                                    </div>
                                    <div class="mt-1 pl-2">
                                        <button v-if="record.id == currentPreviewUpdateId" class="px-3 shadow-md  text-grey bg-transparent border border-gray-600 text-sm hover:bg-blue-700  focus:outline-none"> 
                                            <input type="button" @click="imagePreviewUpdate = null; this.currentPreviewUpdateId= null;"  :id="'cancelUpdateImage_' + record.id" class="hidden"/>
                                        <label :for="'cancelUpdateImage_' + record.id">Cancel</label>
                                        </button>   
                                    </div>
                                </template>

                                 <template v-else-if="column=='orders_to_supplier_id'">
                                      <a href="#"  @click.prevent="openOrderToSuppliersModal(record)" 
                                       class="font-semibold  text-indigo-500 hover:underline"  
                                        >
                                        {{columnValue}}
                                    </a> 
                                </template>

                                  <template v-else-if="column=='img'">                                 
                                         <img :src="columnValue" class="w-20 shadow">
                                </template>

                                  <template v-else-if="column=='received_date'">                                 
                                         <div class="w-20">
                                             <span class="font-medium">
                                                 {{formatTheDate(columnValue)}}
                                                </span>
                                         </div>
                                </template>

                                <template v-else-if="column=='supplier_id'">
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
                    </template> <!-- end Loop through each column of rows-->
                    <!-- Last Column - Actions -->
                        <td  class="items-center">
                            <div v-if="editing.id !== record.id  && editing.id == null">
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
                       
                          <!-- Image Slider Modal -->
                        <div v-if="record.id == clickImgSliderModalId">
                            <Image_Slider_Modal                             
                            :record="record"
                            :table_name="'miscellaneous_invoices'"
                            @close="clickImgSliderModalId=null" 
                            @getRecordForSlider="getRecords" 
                            />
                        </div>

                        <!-- Modal to display Note Modal -->
                        <div v-if="record.id== clickNoteModalId">
                        <Note_Modal  
                            :note="record.Note"
                            :id="record.id" 
                            :theRecord="record" 
                            :table_name="'miscellaneous_invoices'"
                            @close="clickNoteModalId=null" 
                            @refreshRecords="getRecords" 
                        />
                        </div>
                    </tr>
                </tbody>
             <p class="mt-2 text-red-600 text-center text-sm" >Count : {{filteredRecords.length}}</p>
            </table>
        </div>
        <p v-else class="mt-2 text-red-600 text-center text-lg">No Data</p>
        </div>

        
    </div>
    
    </div>
    
</template>



<script>
import moment from 'moment'
import Modal from  '../../components/modal.vue'
import Note_Modal from  './stock_modal/note_modal.vue'
import Goods_Material_Modal from './stock_modal/goods_material_modal.vue'
import Image_Slider_Modal from './stock_modal/imageSliderModal.vue'
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import {mapGetters, mapState } from 'vuex'
import queryString from 'query-string' //use package query-string npm install query-string
export default {
    middleware: [
        //   redirectIfNotCustomer
      ],

    components: {Modal,
    Note_Modal,
    Goods_Material_Modal,
    Image_Slider_Modal,
    Loading},
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
                        // user: this.getAuth.
                        // user:  this.getAuth.user.id,
                        // user:   1,
                        
                        Active: 1,
                        supplier:'NULL',
                        unit_id:1,
                        invoice_type:'Operating_Cost',
                        invoice_category:'NULL',
                        received_date: new Date().toISOString().substr(0, 10), // 05/09/2019
                        paid: 'Yes',
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
                    column: 'received_date',
                    value_1: '',
                    operator_1:'equals',
                    column_1: 'received_date'
                },
                
                isLoading: false,
                fullPage: true,

                selected: [],

                /*****************
                 General Setting
                ******************/

               invoiceCategories:['NULL','Kitchen_Supplies','Meeting/Bonus/Giff','Maintainance','Petrol'],

               invoiceTypes:['NULL','Cost_Of_Goods','Operating_Cost'],
                 
                // Hide Column Section : checkbox of columns that completely disappear
                unshownColumns:['slug','img'],

                // columns hidden - can be show by unclick the radio buttons
                hideColumns:['img','img_three','img_two', 'invoice_category','invoice_type'],


                // columns unshown in edit mode
                unshownColumnsInEditMode: ['img_thumbnail','Note','excel_file',],


                advancedFilterColumns:['id','user','supplier','total_price','received_date'],

                notAllowEditExceptPeopleInCharge: ['total_price'],

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
                ],

                hiddenSelectedColumns:['img'],

                textCenterColumns:['estimated_price'],
                dollarsSymbolColumns:['total_price'],
              


                limit:50,
                quickSearchQuery: '',
                selected_supplier: '',
                selected_user: '',

                selected_dropdown_active: false,

                theDate: new Date().toISOString().substr(0, 10), // 05/09/2019


                //image upload
                image:null,
                imagePreview:null,
                imagePreviewUpdate:null,
                currentPreviewUpdateId:null,

                 // showIMGModal: false,
                clickImgSliderModalId : null,

                clickNoteModalId: null,

                // Level of Users
                firstLevelUsers : ['Admin' , 'Manager'],
                secondLevelUsers : [],

                invoice_total_price: null,
                
               
            }
        },
       
 computed: {
    ...mapGetters({
        getAuth: 'auth/getAuth',
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
            },
            columnsNotAllowToShowAccordingToUserLevel(){
                if(this.getAuth.isFirstLevelUser) {
                    return [] ;
                } else if (this.getAuth.isSecondLevelUser){
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
                } else if (this.getAuth.isSecondLevelUser){
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
                } else if (this.getAuth.isSecondLevelUser){
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

methods: 
{
            
    getRecords(){
        return axios.get(`/api/datatable/miscellaneous_invoices?${this.getQueryParameters()}`).then((response)=> {
            this.response = response.data.data;
        })
    },
    getQueryParameters () {
        return queryString.stringify({
            limit: this.limit,
            supplier: this.selected_supplier,
            user: this.selected_user,
            permission_id: this.selected_permission,
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
       this.isLoading = true;
         axios.patch(`/api/datatable/miscellaneous_invoices/${this.editing.id}`, this.editing.form).then((response) => {
             this.isLoading = false
                this.getRecords().then(() => {
                    this.editing.id = null
                    this.editing.form = null
                })
            }).catch((error) => {
                if (error.response.status === 422) {                        
                    this.editing.errors = error.response.data.errors
                }
            })
    },
    store () {
        this.isLoading = true;

        if(this.image){
            let data = new FormData
            data.append('image', this.image)
            this.creating.form.image = data
        }  
         if(this.getAuth.user) {
            this.creating.form.user = this.getAuth.user.name
        }     

        axios.post(`/api/datatable/miscellaneous_invoices`, this.creating.form).then((response) => {
            this.isLoading = false
           if(response.data.id && this.image) {
                let data = new FormData;
                data.append('image', this.image)
            
            axios.post(`/api/datatable/miscellaneous_invoices/saveImage/${response.data.id}`, data).then((response1)=>{
                this.getRecords().then(() => {
                    this.imagePreview = null;
                    this.image =null;
                })
            }).catch((error) => {
                if (error.response.status === 422) {
                    this.creating.errors = error.response.data.errors                       
                }
            })
            } 

            this.getRecords().then(() => {
                this.creating.active = true
                this.creating.form = {}
                this.creating.errors = []
                this.creating.form.Active=1
                this.creating.form.supplier_id = 1
                this.creating.form.invoice_type ='Operating_Cost'
                this.creating.form.received_date = new Date().toISOString().substr(0, 10) // 05/09/2019
                this.creating.form.paid= 'Yes'
                this.creating.form.supplier= 'NULL'

            })
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
        axios.delete(`/api/datatable/miscellaneous_invoices/${record}`).then(()=>{

            this.selected= [],
            this.selected_dropdown_active = false,
            this.getRecords()
        })      
    },


    openSliderImageModal(record) {        
        this.clickImgSliderModalId = record.id;
    },

    

    makeModalIdNull(modalName) {
        switch (modalName) {
            case 'imageSlider':
               this.clickImgSliderModalId = null; 
                break;
            case 'note':
                this.clickNoteModalId = null; 
                break;
        
            default:
                break;
        }
        
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
        // this.creating.form.imageIcon = e.target.files[0];
        this.image = e.target.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(this.image);

        reader.onload = e => {
             this.imagePreviewUpdate = e.target.result;
             this.currentPreviewUpdateId= id;
        }
    },


    saveImage(invoiceId) {
                    
        if(this.image){
            let data = new FormData
            data.append('image', this.image)
        
         this.isLoading = true;
        axios.post(`/api/datatable/miscellaneous_invoices/saveImage/${invoiceId}`, data).then((response1)=>{
            this.getRecords().then(() => {
                this.isLoading = false
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
    formatTheDateToHourMinute(rawDate){
        return moment(String(rawDate)).format('hh:mm A')
    },
    formatTheDate(rawDate){
        return moment(String(rawDate)).format('DD/MM/YYYY')
    },
    formatTheDateToDateTime(rawDate){
        return moment(String(rawDate)).format('DD/MM/YYYY hh:mm A')
    },


    // makeClickIdNull() {
    //     this.clickImgSliderModalId = null;
    // },
    
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