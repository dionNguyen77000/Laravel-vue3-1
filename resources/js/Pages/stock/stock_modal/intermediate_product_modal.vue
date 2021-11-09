<template>
<div class="backdrop overflow-y-auto" @click.self="closeModal">
  <div class="modal" id="intermediate_product_modal">   
    <button @click="closeModal" type="button" 
        class="w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
        Close
    </button>
        <!-- Intermediate_Product -->
       
        <Intermediate_Product  
            :intermediate_productId="theRecord.intermediate_product_id" 
        />
  </div>
</div>  
</template>



<script>
import Intermediate_Product from  '../intermediate_product.vue'
export default {
    // props: ['orders_to_supplierId'],    
    props: ['theRecord','intermediate_productId','hello'],    
    components: {Intermediate_Product},
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