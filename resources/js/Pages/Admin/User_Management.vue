<template>
  <div id="user_manage" class="p-6"> 
        <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center">
            <div class="w-full p-1">
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Table {{response.table}}</div>
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
                        <div class="w-10/12 md:w-8/12 lg:6/12  p-6 rounded-lg">
                        <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">New {{response.table}}</h3>
                            <form action="#" @submit.prevent="store">
                                <!-- @csrf -->
                                <div class="mb-2" v-for="column in response.updatable" :key="column" >
                                     <template v-if="column=='Active'">
                                        <label  class="font-semibold" :for="column">Active : </label>    
                                        <select class="bg-gray-100 border-2 p-1 rounded-lg" :name="column" :id="column" v-model="creating.form[column]">
                                            <option  value="1">Yes</option>
                                            <option  value="0">No</option>                              
                                        </select>
                                    </template>
                                     <template v-else-if="column=='id'">
                                       
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

                                <div class="mb-2">
                                    <label for="password" class="sr-only"> Password </label>
                                    <input type="password" name="password" id="password" placeholder="password" class="bg-gray-100 border-2 w-full p-1 rounded-lg" 
                                    :class="{ 'border-red-500': creating.errors['password'] }"
                                      v-model="creating.form['password']"
                                     >
                                    <div class="text-red-500 mt-2 text-sm" v-if="creating.errors['password']">
                                         <strong>{{ creating.errors['password'][0] }}</strong>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="password_confirmation" class="sr-only"> Password again </label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="repeat Your Password" class="bg-gray-100 border w-full p-1 rounded-lg" 
                                    v-model="creating.form['password_confirmation']"
                                    >
                                </div>
                                
                                <label  class="font-semibold" for="Active">Roles : </label>    
                                <ul id="roles" class="width-3/4 flex flex-wrap">
                                    <li  class="mr-2" v-for="option,index in response.roleOptions" :key="index">
                                        <input type="checkbox"
                                        
                                        :value="index" 
                                        :id="option" 
                                        v-model="creating.form.assignedRoleIds"
                                        >
                                        {{ option }}
                                    </li>
                                </ul>

                              
                                 <!-- Permissions : 
                                
                                <ul id="permissions" class="width-3/4 flex flex-wrap">
                                    <li  class="mr-2" v-for="option,index in response.permissionOptions" :key="index">
                                        <input type="checkbox"
                                        
                                        :value="index" 
                                        :id="option" 
                                        v-model="creating.form.assignedPermissionIds"
                                        >
                                        {{ option }}
                                    </li>
                                </ul>
                                {{creating.form.assignedPermissionIds}} -->
                                <div class="text-center">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-800 text-white px-4 py-2 rounded">Create</button>
                                </div>
                            
                            </form>

                        </div>
                </div>
         

            <div class="my-2 flex sm:flex-row flex-col">
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
                   <!-- Active And Inactive Filter -->
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
                    {{ column }}
                </li>
                <li class="mr-2">
                    <input type="checkbox" 
                    value="password" 
                    id="password" 
                    :checked="hideColumns.includes('password')"
                    v-model="hideColumns">
                    password
                </li>
                <li class="mr-2">
                    <input type="checkbox" 
                    value="roles" 
                    id="roles" 
                    :checked="hideColumns.includes('roles')"
                    v-model="hideColumns">
                    roles
                </li>
                <li class="mr-2">
                    <input type="checkbox" 
                    value="permissions" 
                    id="permissions" 
                    :checked="hideColumns.includes('permissions')"
                    v-model="hideColumns">
                    permissions
                </li>
            </ul>
          
        </div>
            <!-- start Table -->
                <div class="bg-white shadow-md rounded my-6  overflow-x-auto">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-2" v-if="canSelectItems">
                                     <input type="checkbox" 
                                     @change="toggleSelectAll" 
                                     :checked="filteredRecords.length === selected.length"
                                     >
                                </th>
                              <template v-for="column in response.displayable" :key="column">
                                <th  
                                class="text-left"  
                                v-if="!hideColumns.includes(column)"
                                >
    
                                    <span class="sortable" @click="sortBy(column)">{{column}}</span>
                                    <div 
                                    class="arrow" 
                                    v-if="sort.key===column"
                                    :class="{ 'arrow--asc': sort.order === 'asc', 'arrow--desc': sort.order === 'desc'}">
                                    </div> 
                                </th>
                                </template>

                                <th class="text-left" v-if="!hideColumns.includes('roles')">Roles</th>
                                <th class="text-left" v-if="!hideColumns.includes('permissions')">Permissions</th>
                                <!-- <th class="text-left" v-if="!hideColumns.includes('intermediateProducts')">Intermediates</th> -->
                                <th class="text-left" v-if="!hideColumns.includes('password')">Password</th>

                                <th    class="text-left"  >Actions</th>
                              
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            
                            <tr v-for="record in filteredRecords" :key="record"  class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100">
                                <td v-if="canSelectItems" class=" text-center">
                                    <input type="checkbox" :value="record.id" v-model="selected">
                                </td>

                                <template v-for="columnValue,column in record" :key="column">
                                <td v-if="!hideColumns.includes(column)" class="py-2 text-left">
                                   <!-- If record is updated -->
                                    <template v-if="editing.id === record.id">
                                        <div>
                                        <template v-if="column=='id'">
                                            <input type="text"  
                                            class="w-10 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                            v-model="editing.form[column]"
                                            :class="{ 'border-3 border-red-700': editing.errors[column] }"
                                            > 
                                            <br>
                                            <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                                <strong>{{ editing.errors[column][0] }}</strong>
                                            </span>
                                        </template>
                                        <template v-else-if="column=='roles'">
                                            <ul id="roles" class="ml-2 w-40 flex flex-wrap">
                                                <li  class="w-full" v-for="option,index in response.roleOptions" :key="index">
                                                    <input type="checkbox"
                                                    :value="index" 
                                                    :id="option" 
                                                    v-model="editing.form.assignedRoleIds"
                                                    >
                                                    <label :for="option">{{ option }} ,</label>          
                                                </li>
                                            </ul>
                                        </template>
                                        <template v-else-if="column=='permissions'">
                                            
                                        </template>
                                                  
                                        <template v-else-if="column=='Active'">               
                                            <select
                                            class='w-full rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                            :name="column" :id="column" 
                                            v-model="editing.form[column]">
                                            <option  value="1">Yes</option>
                                            <option  value="0">No</option>
                                        
                                            </select>
                                        </template> 

                                        <template v-else>

                                            <input type="text"  
                                            class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                            v-model="editing.form[column]"
                                            :class="{ 'border-3 border-red-700': editing.errors[column] }"
                                            > 
                                            <br>
                                            <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                                <strong>{{ editing.errors[column][0] }}</strong>
                                            </span>
                                        </template>
                                    
                                        </div>
                                    </template>

                                    <template v-else>
                                        <template v-if="column=='roles'">
                                            <div  class="mr-2 font-medium" v-for="option,index in columnValue" :key="index">
                                                  {{ option.name }}
                                            </div>
                                        </template>  
                                      
                                        <template v-else-if="column=='permissions'">
                                            <div  class="mr-2 font-medium" v-for="option,index in columnValue" :key="index">
                                                  {{ option.name }}
                                            </div>                                                                                             
                                        </template>  

                                        <template v-else-if="column=='email'">
                                            <div class="w-40 flex items-center">
                                                <span class="font-medium">{{columnValue}}</span>
                                            </div>                                                
                                        </template>  


                                         <template v-else>
                                                <div class="flex items-center">
                                                <span class="font-medium">{{columnValue}}</span>
                                            </div>
                                        </template>  
                                    </template>
                                </td>
                                </template>
                                  <!-- <td v-if="!hideColumns.includes('intermediateProducts')" class="py-2  text-left">
                                   
                                    <div class="flex items-center">
                                        <span class="font-medium" >Unshown</span>
                                    </div>
                                   

                                </td> -->
                                <td v-if="!hideColumns.includes('password')" class="py-2  text-left">
                                    <template v-if="editing.id === record.id">
                                    <div class="inline">
                                        <input type="text"  placeholder="Leave Blank if no change"
                                        class="rrounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 "
                                        v-model="editing.form['password_new']"
                                         >
                                    </div>
                                    </template>

                                    <template v-else>
                                    <div class="flex items-center">
                                        <span class="font-medium" >Unshown</span>
                                    </div>
                                    </template>  

                                </td>
                              
                                    <td>
                                     <div v-if="!(record.id==1) || getAuth.isFirstLevelUser">
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
                            </tr>
                        </tbody>
                    <p class="mt-2 text-red-600 text-center text-sm" >Count : {{filteredRecords.length}}</p>

                    </table>
                </div>
            </div>
        </div>
    </div>
    
</template>



<script>

import { mapGetters, mapState } from 'vuex'
// import redirectIfNotCustomer from '../../router/middleware/redirectIfNotCustomer'
import redirectIfNotFirstLevelUser from '../../router/middleware/redirectIfNotFirstLevelUser'
import redirectIfNotSecondLevelUser from '../../router/middleware/redirectIfNotSecondLevelUser'
import queryString from 'query-string' //use package query-string npm install query-string
export default {
  middleware: [
     redirectIfNotFirstLevelUser,redirectIfNotSecondLevelUser
  ],
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
                        assignedPermissionIds: [],
                        assignedRoleIds:[],
                    },
                    errors: [],
                },
                
                editing: {
                    id: null,
                    form: {
                        assignedPermissionIds: [],
                        assignedRoleIds:[],
                    },
                    errors: []
                },
                // search:{
                //     value: '',
                //     operator:'equals',
                //     column: 'id'
                // },
                
                
                selected: [],
                hideColumns:['password','Active','email','mobile','email','intermediates'],
                limit:50,
                quickSearchQuery: '',
                selected_active: '1',
                selected_dropdown_active: false,
                
               
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
            // roleIdNameArrayOfauthenticatedUser() {
            //      let roles = this.getAuth.user.roles;
            //      const roleIdNameArray = [];
            //      roles.forEach(element => {
                     
            //          roleIdNameArray.push(element);
            //      });
            // }
  },

   methods: {
            
            getRecords(){
                // console.log(this.getQueryParameters())
                // return axios.get(`database/users?${this.getQueryParameters()}`).then((response)=> {
                //     this.response = response.data.data;
                // })

                 return axios.get(`/api/datatable/users?${this.getQueryParameters()}`).then((response)=> {
                    this.response = response.data.data;
                    
                })
            },
            getQueryParameters () {
                return queryString.stringify({
                    limit: this.limit,
                    Active: this.selected_active,

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
                this.editing.form.assignedRoleIds = [];
                this.editing.form.assignedPermissionIds = [];
                

                // when click update button, get the current selected ids of the role 
                const rolesOfUser = record.roles;
                for (let index = 0; index < rolesOfUser.length; index++) {
                    const element = rolesOfUser[index];
                  
                     this.editing.form.assignedRoleIds.push(element.id)
                    
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
                axios.patch(`/api/datatable/users/${this.editing.id}`, this.editing.form).then((response) => {
                    this.getRecords().then(() => {
                        this.editing.id = null
                        this.creating.form = {
                            assignedRoleIds:[],
                            assignedPermissionIds:[]
                        }                      
                    })
                }).catch((error) => {
                    if (error.response.status === 422) {                        
                        this.editing.errors = error.response.data.errors
                    }
                })
            },
            store () {    
                axios.post(`/api/datatable/users`, this.creating.form).then((response) => {
                    this.getRecords().then(() => {

                        this.creating.active = true
                        this.creating.form = {
                            assignedRoleIds:[],
                            assignedPermissionIds:[]
                        }
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

                axios.delete(`/api/datatable/users/${record}`).then(()=>{
                    this.selected= [],
                    this.selected_dropdown_active = false,
                    this.getRecords()
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