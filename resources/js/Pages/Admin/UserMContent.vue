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
                        <div class="w-9/12  p-6 rounded-lg">
                        <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">New user</h3>
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
                                
                                <div>
                                    <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded font-medium w-full">Create</button>
                                </div>
                            
                            </form>

                        </div>
                </div>
         

            <div class="my-2 flex sm:flex-row flex-col">
                <div class="flex flex-row mb-1 sm:mb-0">
                     <!-- delete with selected -->
                    <div class="dropdown inline-block relative"  v-if="selected.length">
                        <button 
                        class="border-blue-500 border horver:bg-blue-700 text-gray-700 font-semibold py-1 px-1 inline-flex items-center"
                        @click.prevent="selected_dropdown_active = !selected_dropdown_active"
                        >
                        <span class="text-sm">With Selected</span>
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/> </svg>
                        </button>
                        <ul class="dropdown-menu absolute text-gray-700 pt-1"
                        :class="selected_dropdown_active ? 'block': 'hidden'"
                        >
                        <li class=""><a class=" text-sm bg-blue-200 hover:bg-blue-700 hover:text-white py-1 px-6 block whitespace-no-wrap" href="#" @click.prevent = "destroy(selected)">Delete</a></li>
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
                        <select
                            class="appearance-none h-full rounded-r border-t sm:rounded-r-none sm:border-r-0 border-r border-b block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-2  leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500 text-sm">
                            <option>All</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
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
            
                <div class="bg-white shadow-md rounded my-6  overflow-x-auto">
                    <table class="min-w-max w-full table-auto">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th v-if="canSelectItems">
                                     <input type="checkbox" 
                                     @change="toggleSelectAll" 
                                     :checked="filteredRecords.length === selected.length"
                                     >
                                </th>
                                <th v-for="column in response.displayable" :key="column" class="text-left">
                                    <span class="sortable" @click="sortBy(column)">{{column}}</span>
                                    <div 
                                    class="arrow" 
                                    v-if="sort.key===column"
                                    :class="{ 'arrow--asc': sort.order === 'asc', 'arrow--desc': sort.order === 'desc'}">
                                    </div> 
                                </th>
                                <th class="py-2 text-left">Password</th>
                                <th>&nbsp;</th>
                                 <th>&nbsp;</th>
                              
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            
                            <tr v-for="record in filteredRecords" :key="record"  class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100">
                                <td v-if="canSelectItems" class=" text-center">
                                    <input type="checkbox" :value="record.id" v-model="selected">
                                </td>
                                <td v-for="columnValue,column in record" :key="column" class="py-2 text-left">
                                    <template v-if="editing.id === record.id && isUpdatable(column)">
                                    <div >
                                        <input type="text"  
                                        class="rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 bg-white text-sm text-gray-700 focus:bg-white"
                                        v-model="editing.form[column]"
                                        :class="{ 'border-3 border-red-700': editing.errors[column] }"
                                        > 
                                        <br>
                                        <span v-if="editing.errors[column]" class="text-red-700 font-bold">
                                            <strong>{{ editing.errors[column][0] }}</strong>
                                        </span>
                                    </div>
                                    </template>

                                    <template v-else>
                                    <div class="flex items-center">
                                        <span class="font-medium" v-if="column=='password'">Unshown</span>
                                        <span class="font-medium" v-else>{{columnValue}}</span>
                                    </div>
                                    </template>  

                                   
                                </td>
                                
                                <td class="py-2  text-left">
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
                                <a href="#" @click.prevent="edit(record)"  v-if="editing.id !== record.id">Edit</a>   
                                <template v-if="editing.id === record.id"> 
                               <a href="#" @click.prevent="update"  v-if="editing.id === record.id">Save</a>    <br>
                                <a href="#" @click.prevent="editing.id = null"  v-if="editing.id === record.id">Cancel</a>  
                                </template>
                            </td> 

                            <td>
                                <a href="#" @click.prevent="destroy(record.id)" v-if="response.allow.deletion" >Delete</a>  

                            </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>


<script>

     import queryString from 'query-string' //use package query-string npm install query-string
export default {
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
                    order: 'asc'
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
                // search:{
                //     value: '',
                //     operator:'equals',
                //     column: 'id'
                // },
                
                
                selected: [],
                limit:50,
                quickSearchQuery: '',

                selected_dropdown_active: false,
                
               
            }
        },
       
        computed: {
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

        },

        methods: {
            
            getRecords(){
                console.log(this.getQueryParameters())
                // return axios.get(`database/users?${this.getQueryParameters()}`).then((response)=> {
                //     this.response = response.data.data;
                // })

                 return axios.get(`/api/datatable/users?${this.getQueryParameters()}`).then((response)=> {
                    this.response = response.data.data;
                    // console.log("🚀 ~ file: UserMContent.vue ~ line 208 ~ returnaxios.get ~ response.data", response.data)
                    
                })
            },
            getQueryParameters () {
                return queryString.stringify({
                    limit: this.limit,
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
                axios.patch(`/api/datatable/users/${this.editing.id}`, this.editing.form).then((response) => {
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
                console.log(this.creating.form)
                axios.post(`/api/datatable/users`, this.creating.form).then((response) => {
                // console.log("🚀 ~ file: DataTable.vue ~ line 238 ~ axios.post ~ this.endpoint", this.endpoint
                    this.getRecords().then(() => {
                        this.creating.active = true
                        this.creating.form = {}
                        this.creating.errors = []
                         if(response.data=='successfully created') {
                            alert('User created successfully !')
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