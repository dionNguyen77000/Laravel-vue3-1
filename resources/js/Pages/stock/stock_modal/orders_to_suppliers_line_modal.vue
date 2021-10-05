<template>
<div class="backdrop overflow-y-auto" @click.self="closeModal">
  <div class="modal p-6" id="supplier" > 
    <button @click="closeModal" type="button" 
        class="mt-4 mb-2 w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
        Close
    </button>
        <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center rounded-lg shadow-md">
            <div class="w-full p-1">
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Order Detail</div>
                <div>
                    <a href="#" 
                    class="p-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                    v-if="response.allow.creation"
                    @click.prevent="creating.active = !creating.active">
                    {{ creating.active ? 'Hide' : 'New record' }}
                    </a>
                </div>
                
            </div>

            <div class="flex justify-center" v-if="response.allow.creation && creating.active">
                <div class="w-10/12 md:w-8/12 lg:6/12 p-6 rounded-lg">
                <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">New {{response.table}}</h3>
                    <form action="#" @submit.prevent="store">
                        <!-- @csrf -->
                        <div class="mb-2" v-for="column in response.updatable" :key="column" >
                            <label :for="column" class="sr-only"> </label>
                            <input type="text" :name="column" :id="column" :placeholder="column" class="bg-gray-100 border-2 w-full p-1 rounded-lg"
                            :class="{ 'border-red-500': creating.errors[column] }"
                            v-model="creating.form[column]">
                            <div class="text-red-500 mt-2 text-sm" v-if="creating.errors[column]">
                                    <strong>{{ creating.errors[column][0] }}</strong>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-800 text-white px-4 py-2 rounded">Create</button>
                        </div>
                    
                    </form>

                </div>
        </div>
        
        <!-- show hide column section -->
        <div id="show_hide_section" class="text-center mx-4 space-y-2">
            <p> <b>Show Hide Column </b></p>
            <ul id="hide_show_column_section" class="width-3/4 flex flex-wrap justify-center">
                <li  class="mr-2" v-for="column in response.displayable" :key="column">
                    <input type="checkbox" 
                    :value="column" 
                    :id="column" 
                    :checked="hideColumns.includes(column)"
                    v-model="hideColumns">
                     {{response.custom_columns[column] || column}}
                </li>
            </ul>
          
        </div>

        <!--  -->
        <div  class="flex sm:flex-row flex-col">

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

            <!-- Quick Search Section -->
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
                    <thead>
                        <tr class="collapse py-2 bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-2" v-if="canSelectItems">
                                    <input type="checkbox" 
                                    @change="toggleSelectAll" 
                                    :checked="filteredRecords.length === selected.length"
                                    >
                            </th>
                            <template v-for="column in response.displayable" :key="column">
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
                            <!-- <th class="text-left"  >Actions</th> -->
                            
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        
                        <tr v-for="record in filteredRecords" :key="record"  class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100">
                            <td v-if="canSelectItems" class=" text-center">
                                <input type="checkbox" :value="record.id" v-model="selected">
                            </td>
                            <template v-for="columnValue,column in record" :key="column">
                            <td class="py-2 text-left"  v-if="!hideColumns.includes(column)">
                                <template v-if="editing.id === record.id && isUpdatable(column)">
                                <div >
                                   <template v-if="column=='unit'">
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
                                            <template v-for="option in response.unitOptions" >   
                                                <template v-if="record.id != option.id">                  
                                                <option :value="option" :key="option">
                                                    {{option}} 
                                                </option>
                                                </template>
                                            </template>   
                                        </select>
                                    </template> 

                                    <template v-else-if="column=='category'">
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
                                        <template v-for="option in response.allCategoryOptions" >
                
                                            <template v-if="record.id != option.id" >                  
                                            <option :value="option" :key="option">
                                              {{option}} 
                                            </option>
                                            </template>
                                        </template>                                       
                                    </select>                                   
                                </template> 
                                <template v-else-if="column=='o_line_price'">
                                     <input disabled type="text"  
                                        class="disabled:opacity-50  text-center  rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700"
                                        v-model="editing.form[column]"
                                        
                                        >                           
                                </template>
                                
                                    <template v-else>
                                    
                                        <input type="text"  
                                        class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                        v-model="editing.form[column]"
                                        :class="{ 'border-3 border-red-700': editing.errors[column] , 'text-center': textCenterColumns.includes(column)}"

                                        > 
                                        <br>
                                        <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                            <strong>{{ editing.errors[column][0] }}</strong>
                                        </span>
                                    </template>  
                                    
                                </div>
                                </template>
                                <template v-else>
                                <div :class="{ 'text-center': textCenterColumns.includes(column) }"                                      >                              
                                    <span class="font-medium" >
                                    {{(dollarsSymbolColumns.includes(column) && columnValue != null) ?'$' : '' }}{{columnValue}}
                                    </span>                          
                                </div>
                                </template>   
                            </td>
                            </template>
                            
                            
                            <!-- <td>
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
                            </td>  -->
                        </tr>
                        <tr class="border-b border-gray-200 bg-gray-200 hover:bg-gray-10">
                            <td colspan="100%" class="p-2 text-center text-red-900 text-2xl">
                                Estimated Price: <b class="font-bold">${{order_total_price}}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                <p v-else>No results</p>
        </div>
          
        </div>
    </div>
</div>
</template>



<script>
import {mapGetters, mapState } from 'vuex'
import queryString from 'query-string' //use package query-string npm install query-string
export default {
    props: ['order_to_supplierId','order_total_price'],
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
                    form: {},
                    errors: [],
                },
                editing: {
                    id: null,
                    form: {},
                    errors: []
                },
                
                selected_category: '',
                selected: [],
                hideColumns:['id'],
                textCenterColumns:['o_unit','o_unit_quantity','o_unit_price','o_line_price'],
                dollarsSymbolColumns:['o_unit_price','o_line_price'],
                limit:50,
                quickSearchQuery: '',

                selected_dropdown_active: false,
                
               
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
                // console.log("🚀 ~ file: DataTable.vue ~ line 49 ~ filteredRecords ~ data", data)
                

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
            // console.log(this.getQueryParameters())
            return axios.get(`/api/datatable/order_to_supplier_line?${this.getQueryParameters()}`).then((response)=> {
                this.response = response.data.data;
                console.log("🚀 ~ file: supplier.vue ~ line 305 ~ returnaxios.get ~  this.response",  this.response)
                console.log("record id is: " + this.order_to_supplierId);
            })
        },
        getQueryParameters () {
            return queryString.stringify({
                limit: this.limit,
                category: this.selected_category,
                order_to_supplierId: this.order_to_supplierId
                // ...this.search
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
        toggleSelectAll () {
            if (this.selected.length == this.filteredRecords.length) {
                this.selected = []
                return
            }

            this.selected = _.map(this.filteredRecords, 'id')
        },
        update () {
            axios.patch(`/api/datatable/order_to_supplier_line/${this.editing.id}`, this.editing.form).then((response) => {
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
            axios.post(`/api/datatable/order_to_supplier_line`, this.creating.form).then((response) => {
            // console.log("🚀 ~ file: DataTable.vue ~ line 238 ~ axios.post ~ this.endpoint", this.endpoint
                this.getRecords().then(() => {
                    this.creating.active = true
                    this.creating.form = {

                    }
                    this.creating.errors = []
                        if(response.data=='successfully created') {
                        console.log('created successfully !')
                    } else {
                        alert ('unsucessfully created! please contact Dion')
                    }
                })
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

            axios.delete(`/api/datatable/order_to_supplier_line/${record}`).then(()=>{
                this.selected= [],
                this.selected_dropdown_active = false,
                this.getRecords()
            })
            
        },
         closeModal(){
            this.$emit('close')
        }
            
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

.modal {
    width: 90%;
    height:100%;
    padding: 20px;
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
