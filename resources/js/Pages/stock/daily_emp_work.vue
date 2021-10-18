<template>
    <div id="daily_emp_work" class="p-6"> 
        <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center rounded-lg shadow-md">
            <Loading v-model:active="isLoading"
            :can-cancel="true"
            :is-full-page="fullPage"/> 
            <div class="w-full p-1">
            <!-- New Record Section  -->
            <div>
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Daily Work </div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    v-if="response.allow.creation"
                    @click.prevent="showCreatingForm()">
                    {{ creating.active ? 'Hide' : 'Add Work' }}
                    </a>
                </div>              
            </div>        
            <div class="flex justify-center" v-if="response.allow.creation && creating.active">
                <div class="w-10/12 md:w-8/12 lg:6/12 p-6 rounded-lg">
                <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">Add Work</h3>
                    <form action="#" @submit.prevent="store" enctype="multipart/form-data">
                        <!-- @csrf -->
                        <div class="mb-2" v-for="column in response.updatable" :key="column" >                            
                                <template v-if="column=='date'">
                                <label :for="column" class="sr-only"> </label>
                            <input type="date" :name="column" :id="column" :placeholder="column" class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                            v-model="theDate" disabled>
                            <div class="text-red-500 mt-2 text-sm" v-if="creating.errors[column]">
                                    <strong>{{ creating.errors[column][0] }}</strong>
                            </div>
                            </template>    

                            <template v-else-if="column=='user_id'">
                                <label :for="column" class="font-semibold">Employee : </label>
                                <span > {{response.authenticatedUser.name}} </span>
                                <br>
                                <label for="" class="font-semibold">Role : </label>   
                                <template v-for="role in response.authenticatedUser.roles">
                                    || {{role.name}} &nbsp;
                                </template>
                                <br>
                                <label for="" class="font-semibold"> Permissions : </label>   
                                <template v-for="permission in response.authenticatedUser.permissions">
                                    || {{permission.name}} &nbsp;
                                </template>
                            </template> 

                            <template v-else-if="column=='intermediate_product_id'">
                            <div  :class="{ 'border-red-500 border-2 p-1 rounded-lg': creating.errors[column] }">
                                
                            <label :for="column"  class="font-semibold">Intermediate Product : </label>                          
                            <select class="bg-gray-100 border-2 p-1 rounded-lg" :name="column" :id="column" v-model="creating.form[column]"
                            @change="updateRequiredQty(creating.form[column])"
                            >
                                <option  value="" selected>Select </option>
                                <option :value="option.id" v-for="option,index in response.needPrepareIntermediate_ProductOptions" :key="index">
                                    {{option.name}}
                                </option>
                            </select>
                            </div>
                            <div class="text-red-500 mt-2 text-sm" v-if="creating.errors[column]">
                                    <strong>{{ creating.errors[column][0] }}</strong>
                                </div>
                            </template>    

                            <template v-else-if="column=='role_id'">
                            <label :for="column">Role</label>
        
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option  value=""></option>
                                <option :value="index" v-for="option,index in response.roleOptions" :key="option">
                                    {{option}}
                                </option>
                            </select>
                            </template>   

                            <!-- <template v-else-if="column=='require_qty'">
                            
                            <input type="text" :name="column" :id="column" :placeholder="column" class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                            :class="{ 'border-red-500': creating.errors[column] }"
                            v-model="creating.form[column]">

                            </template> -->
                            <template v-else-if="column=='Status'">
                                <label :for="column"  class="font-semibold">Status : </label>
                                <select class="bg-gray-100 border-2 p-1 rounded-lg" :name="column" :id="column" v-model="creating.form[column]">
                                
                                <option value="OnGoing">
                                    OnGoing
                                </option>

                                    <option value="Completed" >
                                    Completed
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
            </div>   <!-- End New Redord Section -->


            <!-- show hide column section -->
            <div id="show_hide_section" class="text-center mx-4 space-y-2">
                <p> <b> Hide Column </b></p>
                <ul id="hide_show_column_section" class="width-3/4 flex flex-wrap justify-center">
                    <li  class="mr-3" 
                        v-for="column in response.displayable" :key="column"
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
                            <li>
                                <a class=" text-sm bg-blue-200 hover:bg-blue-700 hover:text-white py-1 px-6 block whitespace-no-wrap" href="#" @click.prevent = "destroy(selected)">
                                    Delete
                                </a>
                            </li>
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
                <div class="relative">
                    <select v-model="selected_permission" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option   value= 'All'>Section - All</option>
                         
                        <template v-for="permission in response.userPermissionOptions" :key="permission.id">
                        <!-- <template v-for="permission in response.authenticatedUser" :key="permission.id"> -->
                            <option  :value="permission.id" >
                             <!-- <template v-if="response.userPermissionOptions.includes(option)"> -->
                                    {{permission.name}}
                            <!-- </template> -->                            
                            </option>
                        </template>                          
                    </select>
                    <div
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>

                <div class="flex flex-row mb-1 sm:mb-0">          
                    <div class="relative">
                        <select v-model="selected_status" @change="getRecords"
                            class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option  value= ''>Status</option>         
                            <option v-for="option in response.statusOptions" :value="option" :key="option">
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
            <!-- start Table -->        
            <div  v-if="filteredRecords.length" class="bg-white shadow-md rounded my-3  overflow-x-auto">
                <table class="min-w-max w-full table-auto">
                    <!-- Table Heading Section -->
                    <thead>
                        <tr class="collapse py-2 bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-2" 
                            v-if="(isFirstLevelUser || isSecondLevelUser) && canSelectItems"                          
                            >
                                    <input type="checkbox" 
                                    @change="toggleSelectAll" 
                                    :checked="filteredRecords.length === selected.length"
                                    >
                            </th>
                            <template v-for="column in response.displayable" :key="column">
                                <!-- Table Heading - Edit Mode-->
                                <template v-if="editing.id">
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
                        :class="{ 'bg-green-500 text-white hover:bg-green-600' : record.Status=='OnGoing'}" 
                        >               
                            <td 
                            v-if="(isFirstLevelUser || isSecondLevelUser) && canSelectItems"
                            class=" text-center">
                                <input type="checkbox" :value="record.date+' '+record.user_id+' '+record.intermediate_product_id" v-model="selected">
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
                                <template v-else-if="editing.id ===  record.date+' '+record.user_id+' '+record.intermediate_product_id && isUpdatable(column)">                         
                                    <td class="py-2 text-left" 
                                    :class="{ 'text-center': textCenterColumns.includes(column) }"
                                    v-if="response.displayable.includes(column)">   
                                    <template v-if="editing.id ===  record.date+' '+record.user_id+' '+record.intermediate_product_id && isUpdatable(column)">                         
                                        <template v-if="column=='date'">
                                            <input type="date"  
                                            class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                            v-model="editing.form[column]"
                                            :class="{ 
                                            'border-3 border-red-700': editing.errors[column], 
                                            'text-center': textCenterColumns.includes(column),
                                            'bg-pink-200' : !isFirstLevelUser
                                            }"         
                                            :disabled= "!isFirstLevelUser" 
                                            > 
                                            <br>
                                            <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                                <strong>{{ editing.errors[column][0] }}</strong>
                                            </span>  
                                        </template>    
                                        <template v-else-if="column=='user_id'">
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
                                        <template v-else-if="column=='intermediate_product_id'">
                                            <select
                                            class='rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                            
                                            :name="column" :id="column" v-model="editing.form[column]">
                                            <option :value="option.id" v-for="option,index in response.intermediate_ProductOptions" :key="index">
                                                {{option.name}}
                                            </option>
                                                
                                            </select>
                                            
                                        </template>  
                                        <template v-else-if="column=='Status'">
                                            <!-- {{record}} -->
                                            <select 
                                            class='rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                            :name="column" :id="column" v-model="editing.form[column]">                                        
                                                <option value="OnGoing">
                                                    OnGoing
                                                </option>

                                                <option value="Completed" >
                                                    Completed
                                                </option>  
                                            </select>
                                            
                                        </template>                                      
                                        <template v-else>
                                            <input type="text"  
                                            class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                            v-model="editing.form[column]"
                                            :class="{ 
                                                'border-3 border-red-700': editing.errors[column],
                                                'text-center': textCenterColumns.includes(column)
                                            }"
                                            > 
                                            <br>
                                            <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                                <strong>{{ editing.errors[column][0] }}</strong>
                                            </span>                                  
                                        </template>
                                    </template>                                    
                                </td>
                                </template><!-- End the row current edit -->                             
                                <template v-else>  <!-- Edit Mode - for rows not currently edit -->
                                <td class="py-2 text-left"  
                                :class="{ 'text-center': textCenterColumns.includes(column) }"
                                v-if="!hideColumns.includes(column) || !unshownColumnsInEditMode.includes(column)">
                                <div class="items-center">
                                    <template v-if="column=='date'">
                                        <input type="date"  
                                        class="py-1 font-medium"
                                        :value="columnValue"
                                        :class="{ 'border-3 border-red-700': editing.errors[column] }"
                                        disabled
                                        > 
                                        <br>
                                        <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                            <strong>{{ editing.errors[column][0] }}</strong>
                                        </span>  
                                    </template>   
                                    <template v-else-if="column=='user_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >
                                            {{response.userOptions[columnValue]}}
                                            </span>
                                        </div>
                                    </template>

                                    <template v-else-if="column=='intermediate_product_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >{{response.intermediate_ProductOptions[columnValue].id}} - {{response.intermediate_ProductOptions[columnValue].name}}</span>
                                            <!-- <span class="font-medium" >{{columnValue}}</span> -->
                                            <!-- <span class="font-medium" >
                                                {{response.intermediate_ProductOptions.filter(
                                                response.intermediate_ProductOptions=>(response.intermediate_ProductOptions.id === 1));
                                                }}
                                            </span> -->
                                        </div>
                                    </template>
                                    <template v-else-if="column=='Note'">
                                            <a 
                                            @click.prevent="clickNoteModalId = record.date+' '+record.user_id+' '+record.intermediate_product_id">
                                                <svg v-if="columnValue" class="w-6 h-6 text-red-500 hover:text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>                                     
                                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>                                        
                                            </a>
                                    </template>
                                    <template v-else>
                                        <span  class="font-medium" >{{columnValue}}</span>
                                    </template>
                                </div>
                                </td>                                
                                </template>  <!-- End Edit Mode - the rows current not edit -->
                            </template>   <!-- End Edit Mode -->   
                          <template v-else>   <!-- Not in Edit Mode-->
                                <td class="py-2 text-left"  
                                :class="{ 'text-center': textCenterColumns.includes(column) }"
                                v-if="
                               !hideColumns.includes(column)">
                                <div class="items-center">
                                  
                                    <template v-if="column=='user_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >
                                            {{response.userOptions[columnValue]}}
                                            </span>
                                        </div>
                                    </template>
                                      <!-- <template v-else-if="column=='date'">
                                        <input type="date"  
                                        class="py-1 font-medium"
                                        :value="columnValue"
                                        :class="{ 'border-3 border-red-700': editing.errors[column] }"
                                        disabled
                                        > 
                                        <br>
                                        <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                            <strong>{{ editing.errors[column][0] }}</strong>
                                        </span>  
                                    </template>    -->
                                    <template v-else-if="column=='intermediate_product_id'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >{{response.intermediate_ProductOptions[columnValue].id}} - {{response.intermediate_ProductOptions[columnValue].name}}</span>
                                            <!-- <span class="font-medium" >{{columnValue}}</span> -->
                                            <!-- <span class="font-medium" >
                                                {{response.intermediate_ProductOptions.filter(
                                                response.intermediate_ProductOptions=>(response.intermediate_ProductOptions.id === 1));
                                                }}
                                            </span> -->
                                        </div>
                                    </template>
                                    <template v-else-if="column=='Note'">
                                            <a  @click.prevent="clickNoteModalId = record.date+' '+record.user_id+' '+record.intermediate_product_id">
                                                <svg v-if="columnValue" class="w-6 h-6 text-red-500 hover:text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>                                     
                                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>                                        
                                            </a>
                                    </template>
                                    <!-- <template v-else-if="column=='permission'">
                                            <div class="flex items-center">
                                            <span class="font-medium" >{{response.permissionOptions[columnValue]}}</span>
                                        </div>
                                    </template> -->
                                    <template v-else-if="column=='permission'">
                                        <div  class="mr-2 font-medium" v-for="option,index in columnValue" :key="index">
                                            {{ option.name }}
                                        </div>      
                                    </template> 
                                    <template v-else>
                                        <span  class="font-medium" >{{columnValue}}</span>
                                    </template>
                                </div>
                                </td>
                            </template>                        
                        </template>
                        <td>
                            
                            <div v-if="(record.user_id == getAuth.user.id  &&  today == record.date) || isFirstLevelUser">
                                <a href="#" @click.prevent="edit(record)"  v-if="editing.id !==  record.date+' '+record.user_id+' '+record.intermediate_product_id"
                                class=" mr-1 py-1 px-3 shadow-md rounded-full bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none"
                                >
                                Edit
                                </a>   

                                <a href="#" 
                                @click.prevent="destroy( record.date+' '+record.user_id+' '+record.intermediate_product_id)" 
                                v-if="response.allow.deletion && editing.id !==  record.date+' '+record.user_id+' '+record.intermediate_product_id
                                && canSelectItems" 
                                class=" mr-1 py-1 px-2 shadow-md rounded-full bg-red-400 text-white text-sm hover:bg-red-700 focus:outline-none"
                                >
                                Delete
                                </a>  
                            </div>
                            <div  v-if="editing.id ===  record.date+' '+record.user_id+' '+record.intermediate_product_id">
                            <a href="#" @click.prevent="editing.id = null"  v-if="editing.id ===  record.date+' '+record.user_id+' '+record.intermediate_product_id"
                            class="p-1 bg-transparent border border-gray-600  shadow-md rounded-full  text-grey text-sm hover:bg-green-700 hover:text-white focus:outline-none"
                            >
                            Cancel
                            </a>  

                            <a href="#" @click.prevent="update('all')"  v-if="editing.id ===  record.date+' '+record.user_id+' '+record.intermediate_product_id"
                            class="m-2 py-2 px-3 shadow-md rounded-full bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none"
                            >
                            Save
                            </a>    
                            </div>
                        </td> 
                            
                            <div v-if="record.date+' '+record.user_id+' '+record.intermediate_product_id== clickNoteModalId">
                                <Note_Modal  
                                    :note="record.Note"
                                    :id="record.date+' '+record.user_id+' '+record.intermediate_product_id" 
                                    :theRecord="record" 
                                    :table_name="'daily_emp_work'"
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
import Modal from  '../../components/modal.vue'
import Note_Modal from  './stock_modal/note_modal.vue'
import Loading from 'vue-loading-overlay';
import { mapGetters, mapState } from 'vuex'
import queryString from 'query-string' //use package query-string npm install query-string
export default {
    middleware: [
        //   redirectIfNotCustomer
      ],

    components: {Modal,Note_Modal,Loading},
   data() {
            return {
                response: {
                    table: '',
                    displayable: [],
                    records: [],
                    allow: {

                    },
                },
                sort: {
                    key: 'id',
                    order: 'desc'
                },
                creating: {
                    active: false,
                    form: {
                        Status: 'OnGoing',
                        intermediate_product_id: '',

                    },
                    errors: [],
                },
                editing: {
                    id: null,
                    form: {},
                    errors: []
                },
                // search:{
                //     value: '',
                //     operator:'equals',
                //     column: 'id'
                // },
                
                
                selected: [],
               
                hideColumns:['slug','description','status','role_id','category'],
                hiddenSelectedColumns:['slug','status'],
                unshownColumnsInEditMode:['Note'],
                notAllowEditExceptPeopleInCharge: [],
                textCenterColumns:['current_prepared_qty','required_qty','done_qty'],
                // dollarsSymbolColumns:['price'],
                limit:100,
                quickSearchQuery: '',
                selected_user: '',
                selected_status: '',
                selected_role: '',
                selected_permission: 'All',

                theDate: new Date().toISOString().substr(0, 10), // 05/09/2019
                require_qty: 0,

                today: new Date().toISOString().substr(0, 10), // 05/09/2019

                selected_dropdown_active: false,

                // showModal: false,
                clickThumbnailId : null,

                // noteModal
                clickNoteModalId: null,

                
                isLoading: false,
                fullPage: true,                

                //      // Level of Users
                // firstLevelUsers : ['Admin' , 'Manager'],
                // secondLevelUsers : [],

                
               
            }
        },
       
 computed: {

    ...mapState(['sideBarOpen']),
    ...mapGetters({
            getAuth: 'auth/getAuth',
            firstLevelUsers: 'firstLevelUsers' ,
            secondLevelUsers: 'secondLevelUsers' ,
            thirdLevelUsers: 'thirdLevelUsers' ,
            fourthLevelUsers: 'fourthLevelUsers' ,

        }),
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
             getAllPermissionIds() {
                const allPermissionObjects = this.getAuth.user.permissions;
                const allPermissionObjectIds = _.map(allPermissionObjects,'id')

                return allPermissionObjectIds

                // return allPermissionObjects;
            },
            getRoleNames(){
                const rolNameArray = [];
                const allRoleNames = this.getAuth.user.roles;
                allRoleNames.forEach(element => {
                    rolNameArray.push(element.name)
                });
              
                return rolNameArray;

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
                    });                
                }         
            return rolNameArray;
            },

            isFirstLevelUser() {
                let isCorrect = false;
                for (var i = 0; i < this.firstLevelUsers.length; i++) {
                    if (this.getRoleNames.includes(this.firstLevelUsers[i])) 
                    {
                        isCorrect = true;
                        break;
                    }
                }
                return isCorrect;
            },
            isSecondLevelUser() {
                let isCorrect = false;
                for (var i = 0; i < this.secondLevelUsers.length; i++) {
                    if (this.getRoleNames.includes(this.secondLevelUsers[i])) 
                    {
                        isCorrect = true;
                        break;
                    }
                }
                return isCorrect;
            },
            isThirdLevelUser() {
                let isCorrect = false;
                for (var i = 0; i < this.thirdLevelUsers.length; i++) {
                    if (this.getRoleNames.includes(this.thirdLevelUsers[i])) 
                    {
                        isCorrect = true;
                        break;
                    }
                }
                return isCorrect;
            },
            isFourthLevelUser() {
                let isCorrect = false;
                for (var i = 0; i < this.fourthLevelUsers.length; i++) {
                    if (this.getRoleNames.includes(this.fourthLevelUsers[i])) 
                    {
                        isCorrect = true;
                        break;
                    }
                }
                return isCorrect;
            },

            
    },

methods: 
{
            
    getRecords(){
        return axios.get(`/api/datatable/daily_emp_work?${this.getQueryParameters()}`).then((response)=> {
            this.response = response.data.data;
        })
    },
    getQueryParameters () {
        return queryString.stringify({
            limit: this.limit,
            category_id: this.selected_category,
            user_id: this.selected_user,
            Status: this.selected_status,
            role_id: this.selected_role,
            permission_id: this.selected_permission,

            // ...this.search
        }, 
        {
            skipNull: true
        }
        )
            
    },

    showCreatingForm(){
         this.creating.active = !this.creating.active
    },

    sortBy(column){
    this.sort.key = column
    this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
    },
    edit (record) {
        this.editing.errors = []
        this.editing.id = record.date+' '+record.user_id+' '+record.intermediate_product_id
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

        this.filteredRecords.forEach(element => {
            if(!this.selected.includes(element.date + ' ' + element.user_id + ' ' + element.intermediate_product_id)){
                this.selected.push( element.date + ' ' + element.user_id + ' ' + element.intermediate_product_id)
            }
        });     
    },

    update () {
          if (this.editing.form.Status == 'Completed'){
            const confirmed = window.confirm(" Your Preparation is completed. Remember to add the amount of "
            + this.editing.form.current_prepared_qty + ' to your prepared product in Table Intermediate Product. ?');
             if (!confirmed) {
                 return
            }
        }
        axios.patch(`/api/datatable/daily_emp_work/${this.editing.id}`, this.editing.form).then((response) => {
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
    
        if(this.theDate ) {
            this.creating.form.date = this.theDate
        }
         if(this.getAuth.user) {
            this.creating.form.user_id = this.getAuth.user.id
        }              
        if (this.creating.form.Status == 'Completed'){
            const confirmed = window.confirm(" You choose to complete your preparation. Remember to add the amount of "
            + this.creating.form.current_prepared_qty + ' to your prepared product in Table Intermediate Product. ?');
             if (!confirmed) {
                 return
            }
        }
        axios.post(`/api/datatable/daily_emp_work`, this.creating.form).then((response) => {
            alert ('Done !! Your preparation added in table below') 
            this.getRecords().then(() => {
                this.creating.active = true
                this.creating.form = {}
                this.creating.form.Status = 'OnGoing'
                this.creating.errors = []
            })
        }).catch((error) => {
            if (error.response.status === 422) {
                this.creating.errors = error.response.data.errors                       
            }
        })
    },
        
    destroy(record){
        if(!window.confirm(`Are you sure?`)){
            return
        }
        axios.delete(`/api/datatable/daily_emp_work/${record}`).then(()=>{
            this.selected= [],
            this.selected_dropdown_active = false,
            this.getRecords()
        })
    },

   
    updateRequiredQty(t){
        const  userIntermediate_ProductOptions = JSON.parse(JSON.stringify(this.response.needPrepareIntermediate_ProductOptions))
        for (const key in userIntermediate_ProductOptions) {
            if (userIntermediate_ProductOptions[key].id == t){
                this.creating.form['required_qty'] = userIntermediate_ProductOptions[key].required_qty    
            }
        }
    }

    
},
mounted() {
   
    this.getRecords()
    // this.getAuth
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