<template>
    <div class="backdrop_recipe overflow-y-auto" @click.self="closeModal">   
          <loading v-model:active="isLoading"
            :can-cancel="true"
            :is-full-page="fullPage"/>     
         <div class="modal_recipe p-4" id="recipe" >  
            
            <div class="m-2 w-full flex justify-left">
            <button @click="closeModal" class="rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:text-sm">Close</button>
            </div>           

             <div class="m-2 flex justify-between pt-4">
                <div class="text-2xl text-green-700 font-bold uppercase"> Recipe : {{theRecord.name}}</div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    @click.prevent="creating.active = !creating.active">
                    {{ creating.active ? 'Hide' : 'Add Ingredient' }}
                    </a>
                </div>               
            </div>
            <div v-if="theRecord.description">
                <span class=" p-2 inline-block bg-gray-100 text-red-500 font-bold"> Description: 
                        <span  class="text-yellow-500">{{theRecord.description}} , </span>
                </span>
                
            </div>
            <div v-if="allergies.length">
                <span class=" p-2 inline-block bg-gray-100 text-red-500 font-bold"> Allergies: 
                    <template v-for="allergy in allergies">
                        <span  class="text-yellow-500">{{allergy.name}} , </span>
                    </template>
                </span>
                
            </div>

        <!-- New Record section  -->
        <div class="bg-purple flex justify-center mb-4" v-if="creating.active">
            <div class="w-1/8 rounded-lg">
            <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">Add Ingredident</h3>
                <form 
                class="shadow-md rounded mb-4"
                action="#" @submit.prevent="addIngredient"
                >
                    <!-- @csrf -->
                        <div class="text-center"> 
                                        
                        <label  class="font-semibold" :for="GMIngredient"> GM Ingredient: </label>
                        <select name="selectedGMIngredient" id="selectedGMIngredient"
                        class="m-2 p-1 border border-gray-400 
                        w-full md:w-1/4 lg:w-1/4  rounded-lg"
                        v-model="creating.form['selectedGMIngredient']"
                        >
                            <option :value="index" v-for="option,index in GMOptions" :key="option">
                                {{option}}
                            </option>
                        </select>

                        <input type="text" 
                        name="gmIngredientAmount" 
                        id="gmIngredientAmount" 
                        placeholder="Amount" 
                        class="m-2 p-1 bg-gray-100 border-1 
                        w-full md:w-1/4 lg:w-1/4  rounded-lg"
                        :class="{ 'border-red-500': creating.errors['gmIngredientAmount'] }"
                        v-model="creating.form['gmIngredientAmount']">
                    

                        <select name="gmIngredientType" id="gmIngredientType"
                        class="m-2 p-1 border border-gray-400 
                        w-full md:w-1/4 lg:w-1/4  rounded-lg"
                        v-model="creating.form['gmIngredientType']"
                        >
                            <option value=""> Type </option>
                            <option value="Main"> Main </option>
                            <option value="Seasoning"> Seasoning </option>
                            <option value="Aromatic"> Aromatic </option>
                        </select>
                    </div>
                    <div class="text-center">              
                        <label  class="font-semibold" :for="column"> IP Ingredient: </label>
                        <select name="IPIngredient" id="IPIngredient"
                        class="m-2 p-1 border border-gray-400 
                        w-full md:w-1/4 lg:w-1/4"
                        v-model="creating.form['selectedIPIngredient']"
                        >
                            <option :value="index" v-for="option,index in IPOptions" :key="option">
                                {{option}}
                            </option>
                        </select>
                        <input type="text" 
                        name="ipIngredientAmount" id="ipIngredientAmount" 
                        placeholder="Amount" 
                        class="m-2 p-1 bg-gray-100 border-2
                        w-full md:w-1/4 lg:w-1/4 rounded-lg"
                        :class="{ 'border-red-500': creating.errors[gmIngredientAmount] }"
                        v-model="creating.form['ipIngredientAmount']">

                        <select name="ipIngredientType" id="ipIngredientType"
                        class="m-2 p-1 border border-gray-400 
                        w-full md:w-1/4 lg:w-1/4  rounded-lg"
                        v-model="creating.form['ipIngredientType']"
                        >
                            <option value=""> Type </option>
                            <option value="Main"> Main </option>
                            <option value="Seasoning"> Seasoning </option>
                            <option value="Aromatic"> Aromatic </option>
                        </select>
                    </div>
                    <div class="text-center pb-2">
                        <button type="submit" 
                        class="bg-indigo-500 hover:bg-indigo-800 text-white px-4 py-2 rounded"
                        >
                            Add 
                        </button>
                    </div>
                
                </form>

            </div>
        </div>  <!-- End New Record Section -->

        <div class="mb-2 flex justify-center border-b border-gray-100">  
            <span class="font-semibold text-gray-700 text-2xl">Ingredients</span>
        </div>
  
   
        <!-- show hide column section -->
        <div id="show_hide_section" class="text-center mx-4 space-y-2">
            <p> <b>Show Hide </b></p>
            <ul id="hide_show_column_section" class="width-3/4 flex flex-wrap justify-center">
                <template v-for="column in response.displayable">
                    <template v-if="column == 'intermediate_product_id' || column == 'id'">
                    </template>
                    <template v-else>
                        <li  class="mr-3"  :key="column">                  
                            <input type="checkbox" 
                            :value="column" 
                            :id="column" 
                            :checked="hideColumns.includes(column)"
                            v-model="hideColumns">
                            {{response.custom_columns[column] || column}}
                        </li>
                    </template>
                </template>          
            </ul>        
        </div>

        <div class="flex sm:flex-row flex-col">
               <!-- Type Filter -->
            <div class="block relative">
                <select v-model="selected_type" @change="getRecords"
                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 pl-1 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option  value= ''>Type</option>         
                    <option value="Seasoning">Seasoning</option>
                    <option value="Main" >Main</option>              
                    <option value="Aromatic" >Aromatic</option>              
                </select>
                <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pl-3 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                    </svg>
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

            <div  v-if="filteredRecords.length" class="m-2 bg-white shadow-md rounded overflow-x-auto">
                <table class="min-w-max w-full table-auto border border-green-800">
                <!-- Table Heading Section -->
                    <thead>
                        <tr class="py-8 bg-green-200 text-gray-600 uppercase text-sm md:text-lg leading-normal">
                            
                        <!--  Loop through column in response.displayable  -->
                         <th class="text-center border border-green-600 p-1">
                           ID
                        </th>
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
                                    <th class="text-center border border-green-600 p-1"
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
                                class="p-1 text-center border border-green-600" 
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
                            <th v-if="isFirstLevelUser || isSecondLevelUser" class="text-center ">Actions </th>                          
                        </tr>
                    </thead>
                    <!-- End Table Heading -->
                     <!-- Row (Records) Section -->                    
                    <tbody class="text-gray-600 font-light">
                        <!-- Loop Through each records getting from controller -->
                        <tr v-for="record,index in filteredRecords" :key="record"  class="text-sm md:text-md border-b bg-gray-50 hover:bg-gray-200">                          
                            <td class="w-12 text-center border border-green-600">
                                {{counter = index+1}}
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
                                   
                                    <template v-if="column=='goods_material_id'">
                                        <td class="w-44 p-2 text-center border border-green-600"  
                                        :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="response.displayable.includes(column)"> 
                                        <select
                                        class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                        :class="{
                                            'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column)
                                        }"
                                        :name="column" :id="column" 
                                        v-model="editing.form[column]"
                                        :disabled= " columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
                                        >
                                            <template v-for="option,index in response.GMOptions" >
                    
                                                <template v-if="record.id != option.id" >                  
                                                <option :value="index" :key="option">
                                                {{option}} 
                                                </option>
                                                </template>
                                            </template>                                       
                                        </select>  
                                        </td>                                
                                    </template>   
                                    <template v-else-if="column=='inter_p_ingredient_id'">
                                        <td class="w-44 p-2 text-center border border-green-600"  
                                        :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="response.displayable.includes(column)"> 
                                        <select
                                        class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                        :class="{
                                            'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column)
                                        }"
                                        :name="column" :id="column" 
                                        v-model="editing.form[column]"
                                        :disabled= " columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
                                        >
                                            <template v-for="option,index in response.IPOptions" >
                    
                                                <template v-if="record.id != option.id" >                  
                                                <option :value="index" :key="option">
                                                {{option}} 
                                                </option>
                                                </template>
                                            </template>                                       
                                        </select> 
                                        </td>                                  
                                    </template>   
                                    <template v-else-if="column=='type'">
                                        <td class="py-2 text-center border border-green-600"  
                                        :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="response.displayable.includes(column)"> 
                                        <select
                                        class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                        :class="{
                                            'bg-pink-200' : columnsNotAllowToEditAccordingToUserLevel.includes(column)
                                        }"
                                        :name="column" :id="column" 
                                        v-model="editing.form[column]"
                                        :disabled= " columnsNotAllowToEditAccordingToUserLevel.includes(column)" 
                                        >
                                            <option value="Main"> Main </option>
                                            <option value="Seasoning"> Seasoning </option>
                                            <option value="Aromatic"> Aromatic </option>                                 
                                        </select> 
                                        </td>                                  
                                    </template>   
                                    <template v-else>
                                            <td class="p-2 text-center border border-green-600"  
                                            :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                            v-if="response.displayable.includes(column)"> 
                                            <input type="text"  
                                            class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                            v-model="editing.form[column]"
                                            :class="{ 'border-3 border-red-700': editing.errors[column] }"
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
                                    <template v-if="column=='goods_material_id'">
                                        <td class="w-44 p-2 text-center border border-green-600"  
                                            :class="{ 'text-center': textCenterColumns.includes(column) }"
                                            v-if="response.displayable.includes(column)"
                                            > 
                                            <a href="#"    @click.prevent="openGoodsMaterialModal(record)"                                          
                                                class="font-semibold  text-indigo-500 hover:underline"  
                                                    >                     
                                                <span class="font-medium" >{{response.GMOptions[columnValue]}}</span>
                                            </a> 
                                        </td> 
                                    </template>

                                    <template v-else-if="column=='inter_p_ingredient_id'">
                                        <td class="w-44 p-2 text-center border border-green-600"  
                                            :class="{ 'text-center': textCenterColumns.includes(column) }"
                                            v-if="response.displayable.includes(column)"
                                            > 
                                            <a href="#"    @click.prevent="openIntermediateProductModal(record)"  
                                                class="font-semibold  text-indigo-500 hover:underline"  
                                                    >                                                        
                                                <span class="font-medium" >{{response.IPOptions[columnValue]}}</span>
                                            </a>                                                 
                                        </td> 
                                    </template>

                                    <template v-else-if="column=='amount'">
                                        <td class="p-2 text-center border border-green-600"> 
                                             <span class="font-medium" >{{columnValue}}</span>                                            
                                        </td> 
                                    </template>

                                    <template v-else>
                                        <td class="py-2 text-center border border-green-600"  
                                            :class="{ 'text-center': textCenterColumns.includes(column) }"
                                            v-if="response.displayable.includes(column)"
                                            > 
                                            <span class="font-medium" >{{columnValue}}</span>
                                        </td> 
                                    </template>
                                     
                                </template>
                            </template> <!-- End Not in Edit Mode -->                               
                               
                        </template> <!-- end Loop through each column of rows-->

                         <!-- Last Column - Actions -->  
                            
                        <td v-if="isFirstLevelUser || isSecondLevelUser || isThirdLevelUser" 
                        class='border border-green-600 text-center'
                        >
                            <div>
                                <a href="#" @click.prevent="edit(record)"  v-if="editing.id !== record.id"
                                class=" mr-1 py-1 px-3 shadow-md rounded-full bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none"
                                >
                                Edit
                                </a>   
                                 <a href="#" 
                                @click.prevent="destroy(record.id)" 
                                v-if="response.allow.deletion && editing.id !== record.id 
                                && (isFirstLevelUser || isSecondLevelUser)" 
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

                        <div v-if="record.id == clickGoodsMaterialId">
                            <Goods_Material_Modal  
                            :goods_MaterialId="record.goods_material_id" 
                            @close="clickGoodsMaterialId=null" 
                            />
                        </div>

                        <!-- <div v-if="record.id == clickIntermediateProductId">
                            <Intermediate_Product_Modal  
                            :intermediate_ProductId="record.inter_p_ingredient_id" 
                            @close="clickIntermediateProductId=null" 
                            />
                        </div> -->

                        
                    
                        </tr>
                        <tr> <p class="mt-2 text-red-600 text-center text-xs" >Count : {{filteredRecords.length}}</p></tr>
                    </tbody>
                </table>
            </div>
            <p v-else>No results</p>

           


        <div class="mb-2 flex justify-center border-b border-gray-100">  
            <span class="font-semibold text-gray-700 text-2xl">Instruction</span>
        </div>
        <template v-if="editingInstruction.id == intermediate_id">
            <div class="border-b border-gray-100">
                <editor v-model="recipe"
                class="w-full bg-gray-100 border-2 p-4 rounded-lg shadow-md text-md md:text-xl"
                    api-key="bhvx71arz51on72jvi4rx4kp4pjx3rv273jbms68zyh4u22u"
                    :init="{
                        menubar: false,
                        plugins: 'lists link image emoticons',
                        toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
                    }"
                />     
            </div>    
        </template>
        <template v-else>
            <div v-if="recipe !=null" class="w-full shadow-md shadow-sm">
                <div class='w-full bg-gray-100 border-2 p-4 rounded-lg md:text-xl'
                 >
                 <span v-html="recipe"></span>
                </div>             
            </div>
        </template>   
      	<div  class="px-5 py-4 flex justify-end">
              <template v-if="(theRecord.user_id == getAuth.user.id) || getAuth.isFirstLevelUser || getAuth.isSecondLevelUser">
                    <button @click.prevent="editInstruction()" 
                    v-if="editingInstruction.id !== intermediate_id"
                    class="bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none mr-1 rounded text-sm py-2 px-3">
                        Edit
                    </button>
                    <template v-if="editingInstruction.id == intermediate_id"> 
                        <button @click.prevent="editingInstruction.id = null"
                        class="bg-transparent border border-gray-700 text-sm hover:text-white bg-green-300 hover:bg-green-700 focus:outline-none mr-1 rounded text-sm py-2 px-3">
                            Cancel
                        </button>
                        <button @click.prevent="updateInstruction()"
                        class="bg-blue-500 text-white text-sm hover:bg-blue-700 focus:outline-none mr-1 rounded text-sm py-2 px-3">
                            Save
                        </button>              
                    </template>
              </template>

          </div>
        </div>
    </div>
 

</template>



<script>
import Loading from 'vue-loading-overlay'
import Goods_Material_Modal from './goods_material_modal.vue'
// import Intermediate_Product_Modal from './intermediate_product_modal.vue'
import {mapGetters } from 'vuex'
import Editor from  '@tinymce/tinymce-vue'
import ingredientOption from '../../../components/utility_components/ingredientOption.vue'
import queryString from 'query-string' //use package query-string npm install query-string


export default {
    props: ['intermediate_id','recipe','table_name','theRecord',  'GMOptions','IPOptions','allergies' ],
    components: {
        Loading,
        Goods_Material_Modal,
        // Intermediate_Product_Modal,
        'editor': Editor,
        'ingredientOption' : ingredientOption,
    },
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
                        'ipIngredientType': '',
                        'gmIngredientType': ''
                    },
                    errors: [],
                },

                editing: {
                    id: null,
                    form: {recipe: null},
                    errors: null
                },
                editingInstruction: {
                    id: null,
                    form: {recipe: null},
                    errors: null
                },

                selected_type: '',
                quickSearchQuery: '',

                isLoading: false,
                fullPage: true,

                // showGoodsMaterialsModal: false
                clickGoodsMaterialId : null,

                clickIntermediateProductId : null,

                textCenterColumns:['intermediate_product_id','goods_material_id','inter_p_ingredient_id','amount','type'],


                // Hide Column Section : checkbox of columns that completely disappear
                unshownColumns:['id','intermediate_product_id'],

                  // columns hidden - can be show by unclick the radio buttons
                hideColumns:['inter_p_ingredient_id'],
                // columns unshown in edit mode
                unshownColumnsInEditMode:[],

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

    // getPermissionNames(){
    //     const permissionNameArray = _.map(this.getAuth.user.permissions, 'name');
    //     return permissionNameArray;
    // },
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
        
    getRoleNames(){
        const rolNameArray = [];
        const allRoleNames = this.getAuth.user.roles;
        allRoleNames.forEach(element => {
            rolNameArray.push(element.name)
        });
        
        return rolNameArray;
    },
   
    columnsNotAllowToEditAccordingToUserLevel(){
        if(this.isFirstLevelUser) {
            return [] ;
        } else if (this.isSecondLevelUser){
            return this.secondLevel_ColumnNotAllowsToEdit;
        } 
        else if (this.isThirdLevelUser){
            return this.thirdLevel_ColumnNotAllowsToEdit ;
        } 
        else if (this.isFourthLevelUser){
            return this.fourthLevel_ColumnNotAllowsToEdit ;
        } 
        return this.fourthLevel_ColumnNotAllowsToEdit;
    },

    columnsNotAllowToShowAccordingToUserLevel(){
        if(this.isFirstLevelUser) {
            return [] ;
        } else if (this.isSecondLevelUser){
            return this.secondLevel_ColumnNotAllowsToShow;
        } 
        else if (this.isThirdLevelUser){
            return this.thirdLevel_ColumnNotAllowsToShow;
        } 
        else if (this.isFourthLevelUser){
            return this.fourthLevel_ColumnNotAllowsToShow;
        } 
        return this.fourthLevel_ColumnNotAllowsToShow;
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

  methods: {
        addIngredient() {    
            this.isLoading = true

            axios.post(`/api/datatable/intermediate_product/addIngredient/${this.intermediate_id}`, this.creating.form).then((response) => {
                this.isLoading = false
                this.getRecords().then(() => {
                    this.creating.active = true
                    this.creating.form = {}
                    this.creating.errors = []                   
                })
            }).catch((error) => {
                if (error.response.status === 422) {
                    this.creating.errors = error.response.data.errors
                    
                }
            })
        },

        editInstruction () {
            this.editingInstruction.id = this.intermediate_id
        },
        
        closeModal(){
            this.$emit('close')
        },

        
        refreshRecords(){
            this.$emit('refreshRecords')
        },


        updateInstruction() {
        this.editingInstruction.form.recipe = this.recipe
         axios.post(`/api/datatable/intermediate_product/updateRecipe/${this.intermediate_id}`, this.editingInstruction.form).then((response) => {
                this.editingInstruction.id = null
                this.editingInstruction.form.recipe = null     
            })
        },

          
        getRecords(){
            return axios.get(`/api/datatable/recipe?${this.getQueryParameters()}`).then((response)=> {
                this.response = response.data.data;
                
            })
        },
        getQueryParameters () {
            return queryString.stringify({
                // limit: this.limit,
                // ...this.search
                type: this.selected_type,
                intermediate_id: this.intermediate_id,

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
        },
        isUpdatable (column) {
            return this.response.updatable.includes(column)
        },
        
        update () {
            axios.patch(`/api/datatable/recipe/${this.editing.id}`, this.editing.form).then((response) => {
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
            axios.post(`/api/datatable/recipe`, this.creating.form).then((response) => {
                this.getRecords().then(() => {
                    this.creating.active = true
                    this.creating.form = {}
                    this.creating.form.group = 'Null'
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

            axios.delete(`/api/datatable/recipe/${record}`).then(()=>{
                this.selected= [],
                this.selected_dropdown_active = false,
                this.getRecords()
            })
            
        },

        openGoodsMaterialModal(record) {   
            this.clickGoodsMaterialId = record.id;
        },
        openIntermediateProductModal(record) {   
            this.clickIntermediateProductId = record.id;
        },

            
    },
    mounted() {
        this.getRecords()
    },
    
}

</script>

<style  lang="scss">

 .tox-tinymce-aux{z-index:99999999999 !important;}


.modal_recipe {
    width: 90%;
    margin: 20px auto;
    border-radius: 10px;
    background-color: rgb(255, 255, 255);
}

.backdrop_recipe {
    top:0;
    left:0;
    position: fixed;
    background-color: rgb(0, 0, 0);
    width:100%;
    height:100%;
    z-index: 9999; 
}

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
