<template>

  <div id="goods_material" class="p-6"> 
        <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center rounded-lg shadow-md">      
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :is-full-page="fullPage"/>
        <div class="w-full p-1">
            <!-- New Record section  -->
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Goods & Materials</div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    v-if="response.allow.creation && isFirstLevelUser"
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
                        <div class="mb-2" v-for="column in response.updatable" :key="column" >                          
                            <template v-if="column=='img_thumbnail'">
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
                             <template v-else-if="column=='unit_id'">
                            <label :for="column"  class="font-semibold">Unit : </label>
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option :value="index" v-for="option,index in response.unitOptions" :key="option">
                                    {{option}}
                                </option>
                            </select>
                            </template>    

                            <template v-else-if="column=='category_id'">
                            <label :for="column"  class="font-semibold">Category : </label>
       
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option :value="index" v-for="option,index in response.categoryOptions" :key="option">
                                    {{option}}
                                </option>
                            </select>
                            </template> 

                             <template v-else-if="column=='permission_id'">
                            <label  class="font-semibold" :for="column">Assign To : </label>
       
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option  :value="index" v-for="option,index in response.permissionOptions" :key="option">
                                    <!-- <template v-if="getAuth.user.permissions.includes('edit_posts1')"> -->
                                    {{option}}
                                    <!-- </template> -->

                                </option>
                            </select>
                            </template>    

                             <template v-else-if="column=='supplier_id'">
                            <label :for="column"  class="font-semibold">Supplier :  </label>
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option :value="index" v-for="option,index in response.supplierOptions" :key="option">
                                    {{option}}
                                </option>
                            </select>                     
                            </template>    

                             <template v-else-if="column=='img'"> </template> 
                             <template v-else-if="column=='required_qty'">
                            <input type="text" :name="column" :id="column" :placeholder="column" class=" hidden bg-gray-100 border-2 w-full p-1 rounded-lg"
                            disabled>
                            </template>
                            <template v-else-if="column=='Preparation'">
                            <input type="text" :name="column" :id="column" :placeholder="column" class=" hidden bg-gray-100 border-2 w-full p-1 rounded-lg"
                            disabled>
                            </template>                        
                            <template v-else-if="column=='Active'">
                            <label  class="font-semibold" :for="column">Active : </label>    
                            <select :name="column" :id="column" v-model="creating.form[column]">
                                <option  value="1">Yes</option>
                                <option  value="0">No</option>                              
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

            <!-- Making Order Section  -->
            <div class="flex justify-between pt-4 mb-2">
                <div class="text-2xl font-semibold uppercase"> </div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    v-if="isFirstLevelUser"
                    @click.prevent="placeOrder.active = !placeOrder.active">
                    {{ placeOrder.active ? 'Hide' : 'Place Order' }}
                    </a>
                </div>
            </div>

             <div class="border border-gray-400 shadow flex justify-center" v-if="placeOrder.active">
                <div class="w-10/12 md:w-8/12 lg:6/12 p-6 rounded-lg">
                    <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">Supplier Order</h3>
                    <div class="mb-2 text-center">
                        <label :for="column"  class="font-semibold"> Select Supplier :  </label>                  
                            <select v-model="selected_supplier" @change="selectedSupplierForOrder">
                                <option  value= '' >Supplier</option>
                                <option v-for="option,index in response.supplierOptions" :value="index" :key="option">
                                    {{option}} 
                                </option>                               
                            </select>                        
                    </div>              
                    <template v-if="selected_supplier != ''">
                        <div class="mb-2 text-center">
                            <label  class="font-semibold" >Supplier Email </label>
                            <input type="text" class="bg-gray-100 border-2 p-1 mr-3 rounded-lg"
                            :class="{ 'border-red-500': selectedSupplierInfo.errors['email'] }"
                            v-model="selectedSupplierInfo.email" >
                            
                            <label  class="font-semibold" >Representative </label>
                            <input type="text" class="bg-gray-100 border-2 p-1 mr-3 rounded-lg"
                                v-model="selectedSupplierInfo.representative" disabled
                            >
                            <label  class="font-semibold" >Phone </label>
                            <input type="text" class="bg-gray-100 border-2 p-1 mr-3 ounded-lg"
                                v-model="selectedSupplierInfo.phone" disabled
                            >
                      </div>                    
                        <div class="mb-2 text-center">
                            <h3 class="font-semibold"  >Optional Message</h3>                      
                            <textarea 
                            name="body" id="body" cols="30" rows="3" 
                            class="bg-gray-100 border-2 w-full p-4 rounded-lg shadow-md" 
                            v-model="selectedSupplierInfo.optionalMessage"
                            placeholder="Optional message to supplier ...">
                            </textarea>                          
                        </div>
                        <div class="mb-2 text-center">            
                            <button @click="exportFiles()" 
                            class="p-2 m-2 shadow-md bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none rounded"
                            > 
                            Generate Excel </button>                              
                            <a v-bind:href="orderExcelFileOfSelectedSupplier.link">{{orderExcelFileOfSelectedSupplier.fileName}}</a>
                        </div>                
                        <div class="grid grid-cols-3 gap-4">
                             <button 
                            class="bg-indigo-500 hover:bg-indigo-800 text-white  p-3 rounded "
                             @click="createAndSendOrderToSupplier()"
                            >
                                Create & Email Order
                            </button>
                            <button 
                            class="bg-indigo-500 hover:bg-indigo-800 text-white  p-3 rounded"
                             @click="createOrderToSupplier()"
                            >
                                Create Order
                            </button>

                            <button 
                            class="bg-indigo-500 hover:bg-indigo-800 text-white  p-3 rounded"
                             @click="emailOrderToSupplier()"
                            >
                                Email Order
                            </button>                       
                        </div>
                   </template>
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
            <div class="relative">
                <select v-model="selected_category" @change="getRecords"
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option  value= ''>Category</option>
                        
                    <option v-for="option,index in response.categoryOptions" :value="index" :key="option">
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
                    <select v-model="selected_permission" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option   value= 'All'>Check All</option>
                        <template v-for="permission in getAuth.user.permissions" :key="permission.id">
                            <option  :value="permission.id" >
                             <!-- <template v-if="getAuth.user.permissions.includes(option)"> -->
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
            </div>
            <div class="flex flex-row mb-1 sm:mb-0">          
                <div class="relative">
                    <select v-model="selected_preparation" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option  value= ''>Prep - All</option>

                        <option value="Yes">
                            Prep-Yes
                        </option>
                        <option value= "No" >
                            Prep-No
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
                    <select v-model="selected_active" @change="getRecords"
                        class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        <option  value= ''>Active&Inactive</option>         
                        <option value="0">
                            Inactive
                        </option>
                        <option value="1" >
                            Active
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
                                            <template v-if="column=='unit_id'">
                                                <!-- {{record}} -->
                                                <select 
                                                class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                                :class="{
                                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)
                                                }"
                                                :name="column" :id="column" 
                                                :disabled= "notAllowToEditExceptPeopleInCharge.includes(column)" 
                                                v-model="editing.form[column]"
                                                >
                                                    <template v-for="option,index in response.unitOptions" >   
                                                        <template v-if="record.id != option.id">                  
                                                        <option :value="index" :key="option">
                                                            {{option}} 
                                                        </option>
                                                        </template>
                                                    </template>   
                                                </select>
                                            </template>    
                                            
                                            <template v-else-if="column=='category_id'">
                                                <!-- {{record}} -->
                                                <select
                                                class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                                :class="{
                                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)
                                                }"
                                                :name="column" :id="column" 
                                                v-model="editing.form[column]"
                                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                                >
                                                    <template v-for="option,index in response.categoryOptions" >
                            
                                                        <template v-if="record.id != option.id" >                  
                                                        <option :value="index" :key="option">
                                                        {{option}} 
                                                        </option>
                                                        </template>
                                                    </template>                                       
                                                </select>                                   
                                            </template>  

                                            <template v-else-if="column=='permission_id'">
                                                <!-- {{record}} -->
                                                <select
                                                class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                                :class="{
                                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)
                                                }"                                    
                                                :name="column" :id="column" v-model="editing.form[column]"
                                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 

                                                >
                                                    <template v-for="option,index in response.permissionOptions" >
                            
                                                        <template v-if="record.id != option.id">                  
                                                        <option :value="index" :key="option">
                                                        {{option}} 
                                                        </option>
                                                        </template>
                                                    </template>
                                                    
                                                </select>
                                                
                                            </template>  

                                            <template v-else-if="column=='Preparation'">
                                                <!-- {{record}} -->
                                                <select 
                                                class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                                :class="{
                                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)
                                                }"
                                                :name="column" :id="column" v-model="editing.form[column]"
                                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                                >   
                                                    <option value="Yes">Yes</option>                                  
                                                    <option  value="No">No</option>     
                                                </select>
                                                
                                            </template>    

                                                
                                            <template v-else-if="column=='Active'">
                    
                                                <select
                                                class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                                :class="{
                                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)
                                                }"
                                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                                :name="column" :id="column" 
                                                v-model="editing.form[column]">
                                                <option  value="1">Yes</option>
                                                <option  value="0">No</option>
                                            
                                                </select>
                                            </template> 

                                            <template v-else-if="column=='current_qty'">
                    
                                            <input type="text"  
                                                class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                                v-model="editing.form[column]"
                                                :class="{ 
                                                    'border-3 border-red-700': editing.errors[column] ,
                                                    'text-center': textCenterColumns.includes(column) 
                                                }"
                                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                                > 
                                                <br>
                                                <span v-if="editing.errors[column]" class="text-white font-bold">
                                                    <strong>{{ editing.errors[column][0] }}</strong>
                                                </span>

                                            </template> 

                                            <template v-else-if="column=='supplier_id'">
                                                
                                                <select 
                                                class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                                :class="{
                                                    'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column)
                                                }"
                                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                                :name="column" :id="column" 
                                                v-model="editing.form[column]">                                    >
                                                    <option  value=""></option>
                                                    <template v-for="option,index in response.supplierOptions" >   
                                                        <template v-if="record.id != option.id">                  
                                                        <option :value="index" :key="option">
                                                            {{option}} 
                                                        </option>
                                                        </template>
                                                    </template>
                                                    
                                                </select>
                                                
                                            </template>    

                                            <template v-else>
                                                {{dollarsSymbolColumns.includes(column) ?'$' : '' }} 
                                                <input type="text"  
                                                class='w-3/4 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                            v-model="editing.form[column]"
                                                :class="{ 
                                                'border-3 border-red-700': editing.errors[column] ,
                                                'bg-pink-200' : notAllowToEditExceptPeopleInCharge.includes(column),
                                                'text-center': textCenterColumns.includes(column) 
                                                }"
                                                :disabled= " notAllowToEditExceptPeopleInCharge.includes(column)" 
                                                > 
                                                <br>
                                                <span v-if="editing.errors[column]" class="text-white font-bold">
                                                    <strong>{{ editing.errors[column][0] }}</strong>
                                                </span>
                                            </template>               
                                    </td>                                 
                                </template> <!-- End the row current edit -->
                                <!-- Edit Mode - for rows not currently edit -->
                                <template v-else>
                                    <td class="py-2 text-left"  
                                    :class="{'text-center': textCenterColumns.includes(column) }"
                                    v-if="response.displayable.includes(column)">                                
                                        <div class="items-center">
                                            <template v-if="column=='current_qty'"> 
                                                <div class="text-center">                      
                                                <template v-if="editing.currentQtyId === record.id && isUpdatable(column)">
                                                    <input type="text" 
                                                    class="w-20 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                                    v-model="editing.form[column]"
                                                    :id="column+record.id" 
                                                    :class="{ 'border-3 border-red-700': editing.errors[column] }"
                                                    > 
                                                    <br>
                                                    <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                                        <strong>{{ editing.errors[column][0] }}</strong>
                                                    </span>

                                                    
                                                    <div class="mt-1">
                                                        <button @click.prevent="update('current_qty')"
                                                            class="px-3 shadow-md bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none">                                         
                                                        Save
                                                    </button> 
                                                        <button  @click.prevent="editing.currentQtyId = null" 
                                                            class="ml-2 px-2 shadow-md  text-grey bg-transparent border border-gray-600 text-sm hover:bg-green-700  hover:text-white focus:outline-none">  
                                                        Cancel
                                                    </button>  
                                                    </div>
                                                </template>                                 
                                                <template v-else>
                                                    <button  @click.prevent="editCurrentQty(record)"
                                                    class=" ml-2 py-2 px-4 shadow-md  bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none"
                                                    >
                                                    <span class="font-medium" >{{columnValue}}</span>
                                                    
                                                    
                                                    </button> 
                                                </template>
                                                    </div>
                                            </template>

                                            <template v-else-if="column=='description'">
                                                    <div class="flex items-center w-40">
                                                    <span class="font-medium" >{{columnValue}}</span>
                                                </div>
                                            </template>

                                            <template v-else-if="column=='name'">
                                                    <div class="flex items-center w-24">
                                                    <span class="font-medium" >{{columnValue}}</span>
                                                </div>
                                            </template>

                                            <template v-else-if="column=='unit_id'">
                                                    <div class="flex items-center">
                                                    <span class="font-medium" >{{response.unitOptions[columnValue]}}</span>
                                                </div>
                                            </template>

                                            <template v-else-if="column=='supplier_id'">
                                                    <div class="flex items-center">
                                                    <span class="font-medium" >{{response.supplierOptions[columnValue]}}</span>
                                                </div>
                                            </template>
                                            
                                            <template v-else-if="column=='category_id'">
                                                    <div class="flex items-center">
                                                    <span class="font-medium" >{{response.categoryOptions[columnValue]}}</span>
                                                </div>
                                            </template>
                                       
                                            <template v-else-if="column=='permission_id'">
                                                    <div class="flex items-center">
                                                    <span class="font-medium" >{{response.permissionOptions[columnValue]}}</span>
                                                </div>
                                            </template>

                                            <template v-else>
                                                <span  class="font-medium" >{{columnValue}}</span>
                                            </template>
                                        </div>
                                    </td>  
                                </template> <!-- End Edit Mode - the rows current not edit -->
                            </template>  <!-- End Edit Mode -->
                           
                            <!-- Not in Edit Mode-->
                            <template v-else>
                                <template v-if="hideColumns.includes(column)">
                                </template>
                                <template v-else>
                                    <td class="py-2 text-left"  
                                    :class="{ 'text-center': textCenterColumns.includes(column) }"
                                    v-if="response.displayable.includes(column)">                                
                                        <div class="items-center">
                                            <!-- Thumbnail Image -->
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

                                            <template v-else-if="column=='current_qty'"> 
                                                    <div class="text-center">                      
                                                    <template v-if="editing.currentQtyId === record.id && isUpdatable(column)">
                                                    <input type="text" 
                                                    class="w-20 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                                    :class="{ 
                                                        'border-3 border-red-700': editing.errors[column], 
                                                        'text-center': textCenterColumns.includes(column)               
                                                    }"
                                                    v-model="editing.form[column]"
                                                    :id="column+record.id"                                                    
                                                    > 
                                                    <br>
                                                    <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                                        <strong>{{ editing.errors[column][0] }}</strong>
                                                    </span>

                                                    
                                                    <div class="mt-1">
                                                        <button @click.prevent="update('current_qty')"
                                                            class="px-3 shadow-md bg-blue-300 text-white text-sm hover:bg-blue-700 focus:outline-none">                                         
                                                        Save
                                                    </button> 
                                                        <button  @click.prevent="editing.currentQtyId = null" 
                                                            class="ml-2 px-2 shadow-md  text-grey bg-transparent border border-gray-600 text-sm hover:bg-green-700  hover:text-white focus:outline-none">  
                                                        Cancel
                                                    </button>  
                                                    </div>
                                                </template>                                 
                                                <template v-else>
                                                    <button  @click.prevent="editCurrentQty(record)"
                                                    class=" ml-2 py-2 px-4 shadow-md  bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none"
                                                    >
                                                    <span class="font-medium" >{{columnValue}}</span>
                                               
                                                    </button> 
                                                </template>
                                                    </div>
                                            </template>

                                            <template v-else-if="column=='img'">                                 
                                                <img :src="columnValue" class="w-20 shadow">
                                            </template>

                                            <template v-else-if="column=='description'">
                                                <div class="flex items-center w-40">
                                                <span class="font-medium" >{{columnValue}}</span>
                                                </div>
                                            </template>

                                            <template v-else-if="column=='name'">
                                                <div class="flex items-center w-24">
                                                    <span class="font-medium" >{{columnValue}}</span>
                                                </div>
                                            </template>

                                            <template v-else-if="column=='unit_id'">
                                                <div class="flex items-center">
                                                    <span class="font-medium" >{{response.unitOptions[columnValue]}}</span>
                                                </div>
                                            </template>

                                            <template v-else-if="column=='supplier_id'">
                                                <div class="flex items-center">
                                                    <span class="font-medium" >{{response.supplierOptions[columnValue]}}</span>
                                                </div>
                                            </template>
                                            
                                            <template v-else-if="column=='category_id'">
                                                <div class="flex items-center">
                                                    <span class="font-medium" >{{response.categoryOptions[columnValue]}}</span>
                                                </div>
                                            </template>                                      
                                            <template v-else-if="column=='permission_id'">
                                                <div class="flex items-center">
                                                    <span class="font-medium" >{{response.permissionOptions[columnValue]}}</span>
                                                </div>
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
                            
                        </template>
                         <!-- end Loop through each column of rows-->

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
                        <!-- Modal to display big image when click img_thumbnail -->
                          <!-- <div v-if="record.id== clickThumbnailId">
                            <Modal :recordId="record.id" :bigImg="record.image" @close="makeClickIdNull" />
                        </div> -->

                            <!-- Image Slider Modal -->
                        <div v-if="record.id == clickImgSliderModalId">
                            <Image_Slider_Modal  
                            :record="record"                           
                            :table_name="'goods_material'"
                            @close="clickImgSliderModalId=null" 
                            @getRecordForSlider="getRecords" 
                            />
                        </div>
                    </tr>
                </tbody>
            </table>
        </div>
        <p v-else class="mt-2 text-red-600 text-center text-lg">No Data</p>
        </div>
    </div>
    </div>    
</template>

<script>
import Image_Slider_Modal from './stock_modal/imageSliderModal.vue'
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import {mapGetters, mapState } from 'vuex'
import queryString from 'query-string' //use package query-string npm install query-string
export default {
    middleware: [
        //   redirectIfNotCustomer
      ],

    components: {Loading, Image_Slider_Modal},
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

                placeOrder: {
                    active: false,
                },
                // search:{
                //     value: '',
                //     operator:'equals',
                //     column: 'id'
                // },
                isLoading: false,
                fullPage: true,
                selectedSupplierInfo: {
                    id: null,
                    name: 'work',
                    email: null,
                    phone: null,
                    representative: null,
                    optionalMessage:null,
                    excelFileName:null,
                    errors: [],
                },

                orderExcelFileOfSelectedSupplier: {
                    exit: false,
                    link: null,
                    fileName:null,
                },
       
                selected: [],
                // hideColumns:['slug','description','image'],
                hideColumns:['slug','supplier_id','description','img','price','unit_id',
                'prepared_point','permission_id','category_id','coverage','Active'],

                hiddenSelectedColumns:['slug','img'],
                
                unshownColumnsInEditMode:['img_thumbnail'],

                notAllowEditExceptPeopleInCharge: ['name', 'price','unit_id','supplier_id','category_id','description','prepared_point', 'required_qty','coverage','Preparation','Active'],

                textCenterColumns:['price','current_qty','prepared_point','coverage','required_qty','Preparation','Active'],
                dollarsSymbolColumns:['price'],
                limit:50,
                quickSearchQuery: '',
                selected_supplier: '',
                selected_category: '',
                selected_preparation: 'Yes',
                selected_active: '1',
                selected_permission: 'All',

                //image upload
                image:null,
                imagePreview:null,
                imagePreviewUpdate:null,
                
                currentPreviewUpdateId:null,

                selected_dropdown_active: false,

                // showModal: false,
                clickThumbnailId : null,

                // showIMGModal: false,
                clickImgSliderModalId : null,
                
                // Level of Users
                firstLevelUsers : ['Admin' , 'Manager'],
                secondLevelUsers : [],
                
               
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
    selectedSupplierForOrder(){
        axios.get(`/api/datatable/goods_material/supplierSelection/${this.selected_supplier}`).then((response)=> {
            this.selectedSupplierInfo.id = response.data.id
            this.selectedSupplierInfo.email = response.data.email
            this.selectedSupplierInfo.phone = response.data.phone
            this.selectedSupplierInfo.representative = response.data.representative
            this.getOrderExcelFile();
            this.getRecords();
        })
        
    },    

    createOrderToSupplier(){
        if(this.orderExcelFileOfSelectedSupplier.exit == true){
            this.selectedSupplierInfo.excelFileName = this.orderExcelFileOfSelectedSupplier.link;
            this.isLoading = true
            axios.post(`/api/datatable/goods_material/createOrderToSupplier/${this.selected_supplier}`, this.selectedSupplierInfo).then((response) => {
            this.isLoading = false
            if(response.data) {
                alert('Order created successfully!');
            }
            }).catch((error) => {
                if (error.response.status === 422) {
                    this.selectedSupplierInfo.errors = error.response.data.errors                       
                }

            })
        } else {
            alert('Your excel order file has not been generated. You must generate excel file first.');
        }
       
    },
    emailOrderToSupplier(){

        if(this.orderExcelFileOfSelectedSupplier.exit == true){
            this.selectedSupplierInfo.excelFileName = this.orderExcelFileOfSelectedSupplier.link;           
            this.isLoading = true
            axios.post(`/api/datatable/goods_material/emailOrderToSupplier/${this.selected_supplier}`, this.selectedSupplierInfo).then((response) => {
            this.isLoading = false
            if(response.data) {
                alert('Order email to supplier successfully!');
            }
            }).catch((error) => {
                if (error.response.status === 422) {
                    this.selectedSupplierInfo.errors = error.response.data.errors                       
                }

            })
        } else {

            alert('Your excel order file has not been generated. You must generate excel file first.');
        }
    },

    createAndSendOrderToSupplier(){
        if(this.orderExcelFileOfSelectedSupplier.exit == true){
            this.selectedSupplierInfo.excelFileName = this.orderExcelFileOfSelectedSupplier.link;
            this.isLoading = true
            axios.post(`/api/datatable/goods_material/orderAndEmail/${this.selected_supplier}`, this.selectedSupplierInfo).then((response) => {
            this.isLoading = false
            if(response.data) {
                alert('Order To Supplier created and email to supplier successfully!');
            }
            }).catch((error) => {
                if (error.response.status === 422) {
                    this.selectedSupplierInfo.errors = error.response.data.errors                       
                }
                alert(error.response.data.errors['email'][0]);
            })
        } else {
            alert('Your excel order file has not been generated. You must generate excel file first.');
        }
    },

    getOrderExcelFile() {
        
        axios.get(`/api/datatable/goods_material/checkExcelFileIfGenerated/${this.selected_supplier}`).then((response)=> {
            if (response.data) {
                this.orderExcelFileOfSelectedSupplier.exit = true;

                const arrayExcelFileName = response.data.split("/");
                this.orderExcelFileOfSelectedSupplier.fileName = arrayExcelFileName[arrayExcelFileName.length-1];

                this.orderExcelFileOfSelectedSupplier.link =  response.data;
            } else {
                this.orderExcelFileOfSelectedSupplier.exit = false;
                this.orderExcelFileOfSelectedSupplier.fileName = null;
                this.orderExcelFileOfSelectedSupplier.link =  null;
            }
        })      
    },

    getRecords(){
        // console.log(this.getQueryParameters())
        return axios.get(`/api/datatable/goods_material?${this.getQueryParameters()}`).then((response)=> {
            this.response = response.data.data;
            console.log("🚀 ~ file: goods_material.vue ~ line 336 ~ returnaxios.get ~  this.response",  this.response)
        })
    },
    getQueryParameters () {
        return queryString.stringify({
            limit: this.limit,
            supplier_id: this.selected_supplier,
            category_id: this.selected_category,
            Preparation: this.selected_preparation,
            Active: this.selected_active,
            permission_id: this.selected_permission,
            // ...this.search
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

   update(columnName) {
        if (columnName= 'current_qty' && this.editing.currentQtyId) {
            this.editing.id= this.editing.currentQtyId
            this.editing.currentQtyId = null
        } 
        axios.patch(`/api/datatable/goods_material/${this.editing.id}`, this.editing.form).then((response) => {
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
            }
        })
    },
    
    store () {
        if(this.image){
            let data = new FormData
            data.append('image', this.image)
            this.creating.form.image = data
        }  

        axios.post(`/api/datatable/goods_material`, this.creating.form).then((response) => {
           if(response.data.id && this.image) {
                let data = new FormData;
                data.append('image', this.image)
            
                axios.post(`/api/datatable/goods_material/saveImage/${response.data.id}`, data).then(()=>{
                    this.getRecords().then(() => {
                                
                        this.image =null;
                        this.imagePreview = null;
                        this.imagePreviewUpdate = null;
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
                this.creating.form.Active = 1
                this.creating.form.permission_id = 1
                this.creating.form.supplier_id = 1
                this.creating.form.unit_id = 1
                this.creating.form.category_id = 1   
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
        if(!window.confirm(`Are you sure?`)){
            return
        }
        axios.delete(`/api/datatable/goods_material/${record}`).then(()=>{
            this.selected= [],
            this.selected_dropdown_active = false,
            this.getRecords()
        })
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
        
        axios.post(`/api/datatable/goods_material/saveImage/${productId}`, data).then((response1)=>{
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

    openImageModal(goods_material_id){
        this.clickThumbnailId = goods_material_id;
    },

     openSliderImageModal(record) {        
        this.clickImgSliderModalId = record.id;
    },

    makeClickIdNull() {
        this.clickThumbnailId = null;
    },

    importExcel(){
        axios.post(`/api/datatable/goods_material/fileImport`).then(()=>{           
        })
        
    },
    exportFiles(){      
        this.isLoading = true
        axios.get(`/api/datatable/goods_material/fileExport/${this.selected_supplier}`,
            {responseType: 'arraybuffer'}
        )
        .then((response)=>{
            console.log(response)
                    this.isLoading = false

            this.getOrderExcelFile()
            //code to download file after generating
            // var fileURL = window.URL.createObjectURL(new Blob([response.data]));
            // var fileLink = document.createElement('a');
            // fileLink.href = fileURL;
            // fileLink.setAttribute('download', 'goods.xlsx');
            // document.body.appendChild(fileLink);
            // fileLink.click();
            // console.log(fileLink);
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