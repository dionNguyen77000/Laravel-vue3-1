<template>
<div class="backdrop overflow-y-auto" @click.self="closeModal">
  <div class="modal" id="order_to_suppliers_modal" >       
    <button @click="closeModal" type="button" 
        class="w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
        Close
    </button>
  
        <!-- Orders_To_Suppliers -->
            <Orders_To_Suppliers  
            :orders_to_supplierId="orders_to_supplierId" 
            />
   

  </div>
</div>  
</template>



<script>
import Orders_To_Suppliers from  '../orders_to_suppliers.vue'
export default {
    props: ['orders_to_supplierId'],    
    components: {Orders_To_Suppliers},
    data() {
                return {
                }
            },
       
    computed: {
    
        },

methods: 
{
  
    closeModal(){
        this.$emit('close')
    },
    
},
mounted() {
   
},
    
}

</script>

<style scoped>

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
</style>