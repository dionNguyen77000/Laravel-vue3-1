<template>
<!-- v-if= false -->

    <div  class="backdrop overflow-y-auto" @click.self="closeModal">
       
        <div class="modal">
             <button @click="closeModal" type="button" 
        class="mt-4 mb-2 w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
        Close
        </button>
              <img :src="bigImg" class="w-full lg:w-7/12 h-auto shadow m-auto">
        </div>
    </div>

</template>

<script>
export default {
    props: ['bigImg','recordId'],
    data() {
        return {
            
        }
    },
    methods: {
        closeModal(){
            this.$emit('close')
        }
    }
}
</script>
<style>

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