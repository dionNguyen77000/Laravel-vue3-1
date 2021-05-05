<template>
<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            {{response.table}}
                <a v-if="response.allow.creation" href="#" class="pull-right" @click.prevent="creating.active = !creating.active">{{ creating.active ? 'Hide' : 'New record' }}</a>
        </div>
        <div class="well" v-if="response.allow.creation && creating.active">
            <form action="#" class="form-horizontal" @submit.prevent="store">
                <div class="form-group" v-for="column in response.updatable" :key="column" :class="{ 'has-error': creating.errors[column] }">
                    <label :for="column" class="col-md-3 control-label">{{ response.custom_columns[column] || column}}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" :id="column" v-model="creating.form[column]">
                        <span class="help-block" v-if="creating.errors[column]">
                            <strong>{{ creating.errors[column][0] }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-3">
                        <button class="btn btn-default">Create</button>
                    </div>
                </div>
            </form>
        </div>


        <div class="panel-body">
            <form action="#" @submit.prevent="getRecords">
                    <label for="search">Search</label>
                    <div class="row row-fluid">
                        <div class="form-group col-md-3">
                            <select class="form-control" v-model="search.column">
                                <option :value="column" v-for="column in response.displayable" :key="column">{{ column }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <select class="form-control" v-model="search.operator">
                                <option value="equals">=</option>
                                <option value="contains">contains</option>
                                <option value="starts_with">starts with</option>
                                <option value="ends_with">ends with</option>
                                <option value="greater_than">></option>
                                <option value="less_than"> < </option>
                                <option value="greater_than_or_equal_to">>=</option>
                                <option value="less_than_or_equal_to"><=</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" v-model="search.value">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">Search</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
           <div class="row">
                    <div class="form-group col-md-10">
                        <label for="filter">Quick search current results</label>
                        <input type="text" class="form-control" id="filter" v-model="quickSearchQuery">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="limit">Display records</label>
                        <select class="form-control" id="limit" v-model="limit" @change="getRecords">
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="1000">1000</option>
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
        </div>
    </div>
   
    <div class="panel panel-default">
        <div class="panel-heading" v-if="selected.length">
            <div class="btn-group">
                <a href="#" data-toggle="dropdown">with selected <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="#" @click.prevent = "destroy(selected)">Delete</a></li>
                </ul>
            </div>
        </div>
         <div class="panel-body">
         <div class="table-responsive" v-if="filterRecords.length">
                <table class="table table-striped">
                    <thead>
                        <tr>
                             <th v-if="canSelectItems">
                                    <input type="checkbox" @change="toggleSelectAll" :checked="filterRecords.length === selected.length">
                                </th>
                            <th v-for="column in response.displayable" :key="column">
                                <span class="sortable" @click="sortBy(column)">{{response.custom_columns[column] || column}}</span> 

                                <div 
                                class="arrow" 
                                v-if="sort.key===column"
                                :class="{ 'arrow--asc': sort.order === 'asc', 'arrow--desc': sort.order === 'desc'}">
                                
                                </div> 
                            </th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="record in filterRecords" :key="record.id">
                             <td v-if="canSelectItems">
                                    <input type="checkbox" :value="record.id" v-model="selected">
                                </td>
                            <!-- {{record}} -->
                           <td v-for="columnValue, column in record" >
                               <template v-if="editing.id === record.id && isUpdatable(column)">
                                    <div class="form-group" :class="{ 'has-error': editing.errors[column] }">
                                        <input type="text" class="form-control" v-model="editing.form[column]">
                                        <span class="help-block" v-if="editing.errors[column]">
                                            <strong>{{ editing.errors[column][0] }}</strong>
                                        </span>
                                    </div>
                                </template>
                                <template v-else>
                                    {{ columnValue }}
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
             <p v-else>No results</p>
             </div>
    </div>
</div>
    
</template>

<script>
    import queryString from 'query-string'
    export default {
        props: ['endpoint'],
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
                search:{
                    value: '',
                    operator:'equals',
                    column: 'id'
                },
                
                
                selected: [],
                limit:50,
                quickSearchQuery: '',
                
               
            }
        },
       
        computed: {
            filterRecords () {
                let data = this.response.records;
                // console.log("🚀 ~ file: DataTable.vue ~ line 49 ~ filterRecords ~ data", data)
                
                data = data.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLowerCase().indexOf(this.quickSearchQuery.toLowerCase()) > -1
                    })
                })
                
                if (this.sort.key) {
                    data = _.orderBy(data, (i) => {
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
                return this.filterRecords.length <= 500
            },

        },

        methods: {
            
            getRecords(){
                // console.log(this.getQueryParameters())
                return axios.get(`${this.endpoint}?${this.getQueryParameters()}`).then((response)=> {
                    this.response = response.data.data;
                })
            },
            getQueryParameters () {
                return queryString.stringify({
                    limit: this.limit,
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
            },
            isUpdatable (column) {
                return this.response.updatable.includes(column)
            },
            toggleSelectAll () {
                if (this.selected.length == this.filterRecords.length) {
                    this.selected = []
                    return
                }

                this.selected = _.map(this.filterRecords, 'id')
            },
            update () {
                axios.patch(`${this.endpoint}/${this.editing.id}`, this.editing.form).then(() => {
                    this.getRecords().then(() => {
                        this.editing.id = null
                        this.editing.form = null
                    })
                }).catch((error) => {
                    if (error.response.status === 422) {                        
                        this.editing.errors = error.response.data.errors
                        // console.log("🚀 ~ file: DataTable.vue ~ line 262 ~ axios.patch ~ this.editing.errors", this.editing.errors)
                    }
                })
            },
            store () {
                axios.post(`${this.endpoint}`, this.creating.form).then(() => {
                // console.log("🚀 ~ file: DataTable.vue ~ line 238 ~ axios.post ~ this.endpoint", this.endpoint)
                    this.getRecords().then(() => {
                        this.creating.active = false
                        this.creating.form = {}
                        this.creating.errors = []
                    })
                }).catch((error) => {
                    if (error.response.status === 422) {
                        this.creating.errors = error.response.data.errors
                        
                    }
                })
            },
            destroy(record){

                if(!window.confirm(`Are you sure you want to delete this?`)){
                    return
                }

                axios.delete(`${this.endpoint}/${record}`).then(()=>{
                    this.selected= []
                    this.getRecords()
                })
                
            },
            
        },

         mounted() {
            this.getRecords()
        },
    }
</script>

<style lang="scss" scoped>
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
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #222;

        }

         &--desc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #222;

        }
    }

</style>
