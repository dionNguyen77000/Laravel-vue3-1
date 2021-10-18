<template>
<div class="backdrop overflow-y-auto" @click.self="closeModal">
  <div class="modal" id="goods_material_modal">       
    <button @click="closeModal" type="button" 
        class="w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
        Close
    </button>
  
        <!-- Goods_Material -->
            <Goods_Materials  
            />
   

  </div>
</div>  
</template>



<script>
import Goods_Materials from  '../goods_material.vue'
export default {
    props: ['orders_to_supplierId'],    
    components: {Goods_Materials},
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

        refreshRecords(){
            this.$emit('refreshRecords')
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