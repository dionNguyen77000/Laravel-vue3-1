<template>
<div class="backdrop overflow-y-auto" @click.self="closeModal">
  <div class="modal" id="goods_material_modal">       
    <button @click="closeModal" type="button" 
        class="w-full rounded-md border border-gray-300 shadow-sm mb-1 px-4 py-2 
        bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none 
        focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
        Close
    </button>

     <loading v-model:active="isLoading"
                 :can-cancel="true"
                 :is-full-page="fullPage"/>
    
        
    <div class="min-w-screen min-h-screen bg-gray-100 flex justify-center rounded-lg shadow-md">
        <div class="w-full p-1">  
            <div class="flex justify-between pt-4">
                <div class="text-2xl font-semibold uppercase"> Order Placement</div>        
            </div>       
                <h3 class="text-xl text-gray text-center font-bold  p-3 mb-1">Supplier Order</h3>
                <div class="mb-2 text-center">
                <label  class="font-semibold"> Supplier :  {{selectedSupplierInfo.name}}</label>                                                         
            </div>  
            <div>
            <!-- <span>{{}}</span> -->
              <!-- <pre>{{ requiredRecords }}</pre> -->
            </div>  

            <div class="mb-2 text-center">
                <div class="mb-2">
                    <label  class="font-semibold" >Supplier Email </label>
                    <input type="text" class=" text-center bg-gray-100 border-2 p-1 mr-3 rounded-lg"
                    v-model="selectedSupplierInfo.email" >
                </div>
                <div class="mb-2">
                    <label  class="font-semibold" >Representative </label>
                    <input type="text" class=" text-center bg-gray-100 border-2 p-1 mr-3 rounded-lg"
                    v-model="selectedSupplierInfo.representative" disabled
                    >
                </div>
                <div class="mb-2">
                    <label  class="font-semibold" >Supplier Phone </label>
                    <input type="text" class=" text-center bg-gray-100 border-2 p-1 mr-3 rounded-lg"
                        v-model="selectedSupplierInfo.phone" disabled
                    >
                </div>
                
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
                <button @click="exportFiles1()" 
                class="p-2 m-2 shadow-md bg-green-500 text-white text-sm hover:bg-green-700 focus:outline-none rounded"
                > 
                    Generate Excel 
                </button>                              
                <a v-bind:href="orderExcelFileOfSelectedSupplier.link">{{orderExcelFileOfSelectedSupplier.fileName}}</a>
            </div>      
                    
            <div class="grid grid-cols-3 gap-4">
            
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
                <button 
                class="bg-indigo-500 hover:bg-indigo-800 text-white  p-3 rounded "
                    @click="createAndSendOrderToSupplier()"
                >
                    Create & Email Order
                </button>                   
            </div>

            <div id="show_hide_section" class="text-center mx-3 my-3 space-y-2">
                <p> <b>Hide Column </b></p>
                <ul id="hide_show_column_section" class="width-3/4 flex flex-wrap justify-center">
                    <li  class="mr-3" v-for="column in displayable" :key="column"
                    :class="{ 
                        'hidden': unshownColumns.includes(column) ||  columnsNotAllowToShowAccordingToUserLevel.includes(column),
                    }"
                    >
                        <input type="checkbox" 
                        :value="column" 
                        :id="column" 
                        :checked="hideColumns.includes(column)"
                        v-model="hideColumns">
                        {{custom_columns[column] || column}}
                    </li>
                    
                </ul> 
            </div> 


             <div class="w-1/2 block relative">
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
            
              
            <!-- start Table -->        
            <div  v-if="requiredfilteringRecords.length" class="bg-white shadow-md rounded my-3  overflow-x-auto">
                <table class="min-w-max w-full table-auto">
                    <!-- Table Heading Section -->
                    <thead>
                        <tr class="collapse py-2 bg-blue-200 text-gray-600 uppercase text-sm leading-normal">
                         <!-- Table Heading - Columns not allow to show in any mode-->
                      
                            <th 
                            v-if="(getAuth.isFirstLevelUser || getAuth.isSecondLevelUser ||  getAuth.isThirdLevelUser)"
                            class="p-1"
                            >
                                <input type="checkbox" 
                                @change="toggleSelectAll" 
                                :checked="requiredfilteringRecords.length === selected.length"
                                >
                            </th>
                        <!--  Loop through column in displayable  -->
                        <template v-for="column in displayable" :key="column">
                            <!-- Table Heading - Columns not allow to show in any mode-->
                            <template v-if="unshownColumns.includes(column) || hideColumns.includes(column)">
                            </template>
                            <!-- Table Heading - Columns not allow to show according to the level of user-->
                            <template v-else-if="columnsNotAllowToShowAccordingToUserLevel.includes(column)">
                            </template>

                            <template v-else>
                                <th class="text-left p-1"
                                :class="{ 'text-center': textCenterColumns.includes(column) }"
                                >
                                    <span class="sortable" @click="sortBy(column)"> {{custom_columns[column] || column}}</span>
                                    <div 
                                    class="arrow" 
                                    v-if="sort.key===column"
                                    :class="{ 'arrow--asc': sort.order === 'asc', 'arrow--desc': sort.order === 'desc'}">
                                    </div> 
                                
                                </th>
                            </template>
                    
                        </template>    
                       
                    </tr>  
                    </thead>
                    <!-- End Table Heading -->
                    <!-- Row (Records) Section -->
                    <tbody class="text-gray-600 text-sm font-light">
                        <tr v-for="record,index in requiredfilteringRecords" :key="record"  class="border-b border-gray-200 bg-gray-50 hover:bg-gray-100">
                          
                            <td v-if="(getAuth.isFirstLevelUser || getAuth.isSecondLevelUser || getAuth.isThirdLevelUser)" class=" text-center">
                                <input type="checkbox" :value="record.id" v-model="selected">
                            </td>
                           
                           <template v-for="columnValue,column in record" :key="column">
                              <!-- Columns not allow to show in any mode-->
                                <template v-if="unshownColumns.includes(column) || hideColumns.includes(column)">
                                </template>
                                <!-- Table Heading - Columns not allow to show according to the level of user-->
                                <template v-else-if="columnsNotAllowToShowAccordingToUserLevel.includes(column)">
                                </template>

                                <template v-else>
                                                         
                                <template v-if= "column=='name'">
                                    <td class="w-36 py-2 text-left"  
                                        :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                    >
                                        <span class="font-medium" >{{columnValue}}</span>
                                    </td>
                                </template>
                                <!-- <template v-else-if= "column=='O_Status'">
                                    <td class="w-36 py-2 text-left"  
                                        :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                    >
                                        <span class="font-medium" >{{columnValue}}</span>
                                    </td>
                                </template> -->
                                <template v-else-if= "column=='price'">
                                    <td class="py-2 text-left" 
                                     :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                    >
                                        {{dollarsSymbolColumns.includes(column) ?'$' : '' }}
                                        <span class="font-medium" >{{columnValue}}</span>
                                    </td>
                                </template>

                                <template v-else-if="column=='unit_id'">
                                    <td class="py-2 text-left"  
                                    :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                    >
                                        <span class="font-medium" >{{unitOptions[columnValue]}}</span>
                                    </td>
                                </template> 
                                <template v-else-if="column=='category_id'">
                                    <td class="py-2 text-left"  
                                    :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                    >
                                        <span class="font-medium" >{{categoryOptions[columnValue]}}</span>
                                    </td>
                                </template> 
                                <template v-else-if="column=='required_qty'">
                                    <td class="w-28 py-2 text-left"  
                                    :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                        
                                    >
                                        <input type="text"  
                                        class='w-3/4 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                        :class="{ 
                                            'text-center': textCenterColumns.includes(column) 
                                            }"                                                
                                            v-model="requiredfilteringRecords[index].required_qty"    
                                        > 
                                    </td>
                                </template> 

                                <template v-else>
                                    <!-- <td class="py-2 text-left" 
                                    :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                    > 
                                     {{dollarsSymbolColumns.includes(column) ?'$' : '' }} 
                                    <input type="text"  
                                    class='w-3/4 rounded-r rounded-l sm:rounded-l-none border border-gray-400 pl-1 pr-1 py-1 text-sm text-gray-700'
                                        :class="{ 
                                        'text-center': textCenterColumns.includes(column) 
                                        }"
                                    > 

                                    </td> -->

                                    <td class="w-24 py-2 text-left"  
                                        :class="{ 'text-center': textCenterColumns.includes(column)}" 
                                        v-if="displayable.includes(column)"
                                    >
                                        <span class="font-medium" >{{columnValue}}</span>
                                    </td>
                                </template>                               
                                </template>
                            </template>
                        </tr>   
                    </tbody>
                <p class="mt-2 text-red-600 text-center text-sm" >Count : {{requiredfilteringRecords.length}}</p>
                </table>
            </div>    
        </div>
    </div>
  </div>
</div>  
</template>



<script>
// import Goods_Materials from  '../goods_material.vue'
import Loading from 'vue-loading-overlay';
import {mapGetters} from 'vuex'
export default {
    props: ['requiredRecords', 'categoryOptions','unitOptions','custom_columns','selectedSupplierInfo' ],    
    // components: {Goods_Materials},
    components: {Loading},

    data() {
        return {

            displayable : [
                // 'id',
                'name',
                'price',
                'unit_id',
                'category_id',
                // 'current_qty','prepared_point','coverage',
                'required_qty',
                'O_Status',
            ],
            hideColumns:[],            
            unshownColumns: [],
            columnsNotAllowToShowAccordingToUserLevel: [],
            textCenterColumns: ['price','required_qty'],
            selected: [],
            dollarsSymbolColumns:['price'],

            isLoading: false,
            fullPage: true,
              
            sort: {
                key: 'category_id',
                order: 'asc'
            },

            quickSearchQuery: '',


            orderExcelFileOfSelectedSupplier: {
                exit: false,
                link: null,
                fileName:null,
            },

        }
    },
       
    computed: {
        ...mapGetters({
        getAuth: 'auth/getAuth'
        }),
        requiredfilteringRecords () {
            // return this.response.records;
            let data = this.requiredRecords;
            
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

        
    },

    methods: 
    {
        sortBy(column){
            this.sort.key = column
            this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'            
        },
        toggleSelectAll () {
            if (this.selected.length == this.requiredfilteringRecords.length) {
                this.selected = []
                return
            }

            this.selected = _.map(this.requiredfilteringRecords, 'id')
        },

        selectedSupplierForOrder($supplierId){
                axios.get(`/api/datatable/goods_material/supplierSelection/${this.selectedSupplierInfo.id}`).then((response)=> {
                    this.selectedSupplierInfo.id = response.data.id
                    this.selectedSupplierInfo.name = response.data.name
                    this.selectedSupplierInfo.email = response.data.email
                    this.selectedSupplierInfo.phone = response.data.phone
                    this.selectedSupplierInfo.representative = response.data.representative
                    this.getOrderExcelFile();
                    this.getRecords();
                })
                
        },    
         getOrderExcelFile() {
        
            axios.get(`/api/datatable/goods_material/checkExcelFileIfGenerated/${this.selectedSupplierInfo.id}`).then((response)=> {
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

        exportFiles(){      
            this.isLoading = true
            axios.get(`/api/datatable/goods_material/fileExport/${this.selectedSupplierInfo.id}`, this.requiredfilteringRecords,
                {responseType: 'arraybuffer'}
            )
            .then((response)=>{
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
        exportFiles1(){    
            if(this.selected.length < 1){
                alert('Please select at lease one product by ticking check-box in the table below')
                return;
            }  
            this.isLoading = true

            let excelDataArray = []
            this.requiredfilteringRecords.forEach(element => {
                if(this.selected.includes(element.id)){
                    excelDataArray.push(
                        {
                            id: element.id,
                            name: element.name,
                            price: element.price,
                            unit_id: element.unit_id,
                            category_id: element.category_id,
                            required_qty: element.required_qty,
                        }
                    )
                }
            });



            axios.post(`/api/datatable/goods_material/fileExport1/${this.selectedSupplierInfo.id}`, excelDataArray,
                {responseType: 'arraybuffer'}
            )
            .then((response)=>{
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

        createOrderToSupplier(){         
            // check if the items are already ordered
            let numOfOrderedItems = this.itemsAlreadyOrdered()
            let notProceed = false

            // ask if want to proceed order if there is some items already ordered
            if(numOfOrderedItems > 0 ){
                notProceed = window.confirm('You choose to order your items below. However, '+numOfOrderedItems + ' items have been orderd and waiting them to arrive. Do you want to proceed anyway? ')
                if(notProceed== false){
                   return
                }
            }


            // if(this.orderExcelFileOfSelectedSupplier.exit == true){
                this.isLoading = true
                // get file name
                this.selectedSupplierInfo.excelFileName = this.orderExcelFileOfSelectedSupplier.link;
                
                // set up send data
                let excelDataArray = []
                this.requiredfilteringRecords.forEach(element => {
                    if(this.selected.includes(element.id)){
                        excelDataArray.push(
                            {
                                id: element.id,
                                name: element.name,
                                price: element.price,
                                unit_id: element.unit_id,
                                category_id: element.category_id,
                                required_qty: element.required_qty,
                            }
                        )
                    }
                });

                let postData = {
                    excelData: excelDataArray,
                    supplierInfo: this.selectedSupplierInfo
                };


                axios.post(`/api/datatable/goods_material/createOrderToSupplier/${this.selectedSupplierInfo.id}`, postData).then((response) => {
                    this.isLoading = false
                    if(response.data) {
                        alert('Order created successfully! Remember to refresh your browser to see new order');
                    }
                }).catch((error) => {
                    if (error.response.status === 422) {
                        this.selectedSupplierInfo.errors = error.response.data.errors                       
                    }
                })

            // } else {
            //     alert('Your excel order file has not been generated. You must generate excel file first.');
            // }
        
        },

        emailOrderToSupplier(){
            if(this.orderExcelFileOfSelectedSupplier.exit == true){
                this.isLoading = true
                
                this.selectedSupplierInfo.excelFileName = this.orderExcelFileOfSelectedSupplier.link;           

                // set up send data
                let excelDataArray = []
                this.requiredfilteringRecords.forEach(element => {
                    if(this.selected.includes(element.id)){
                        excelDataArray.push(
                            {
                                id: element.id,
                                name: element.name,
                                price: element.price,
                                unit_id: element.unit_id,
                                category_id: element.category_id,
                                required_qty: element.required_qty,
                            }
                        )
                    }
                });

                let postData = {
                    excelData: excelDataArray,
                    supplierInfo: this.selectedSupplierInfo
                };

                axios.post(`/api/datatable/goods_material/emailOrderToSupplier/${this.selectedSupplierInfo.id}`, postData).then((response) => {
                this.isLoading = false
                if(response.data) {
                    alert('Order email to supplier successfully! Remember to refresh your browser to see new order');
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
        
            // check if the items are already ordered
            let numOfOrderedItems = this.itemsAlreadyOrdered()
            let notProceed = false

            // ask if want to proceed order if there is some items already ordered
            if(numOfOrderedItems > 0 ){
                notProceed = window.confirm('You choose to order your items below. However, '+numOfOrderedItems + ' items have been orderd and waiting them to arrive. Do you want to proceed anyway? ')
                if(notProceed== false){
                   return
                }
            }
       
            if(this.orderExcelFileOfSelectedSupplier.exit == true){

                this.isLoading = true
                this.selectedSupplierInfo.excelFileName = this.orderExcelFileOfSelectedSupplier.link;

                 // set up send data
                let excelDataArray = []
                this.requiredfilteringRecords.forEach(element => {
                    if(this.selected.includes(element.id)){
                        excelDataArray.push(
                            {
                                id: element.id,
                                name: element.name,
                                price: element.price,
                                unit_id: element.unit_id,
                                category_id: element.category_id,
                                required_qty: element.required_qty,
                            }
                        )
                    }
                });

                let postData = {
                    excelData: excelDataArray,
                    supplierInfo: this.selectedSupplierInfo
                };

                axios.post(`/api/datatable/goods_material/orderAndEmail/${this.selectedSupplierInfo.id}`, postData).then((response) => {
                this.isLoading = false
                if(response.data) {
                    alert('Order To Supplier created and email to supplier successfully! Remember to refresh your browser to see new order');
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
        
        itemsAlreadyOrdered(){          
            let count = 0
            this.requiredfilteringRecords.forEach(filteringRecord => {
                if(filteringRecord.O_Status == 'waiting'){
                    count++
                }
            });
            return count;
        },
        closeModal(){
            this.$emit('close')
        },

        

        // refreshRecords(){
        //     this.$emit('refreshRecords')
        // },

        
    },
    mounted() {
        this.getOrderExcelFile()
        // selecte all of items at beginning
        this.selected = _.map(this.requiredfilteringRecords, 'id')
    },
    
}

</script>

<style lang="scss">

.modal {
    width: 90%;
    height:100%;
    margin: 30px auto;
    border-radius: 10px;
    background-color: black;
}

.backdrop {
    top:0;
    left:0;
    position: fixed;
    background: rgba(0,0,0,1);
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