<template>
    <div class=" backdrop flex flex-col justify-center items-center">
       
         <div class="w-full md:w-2/3 sm:w-full rounded-lg shadow-lg bg-white my-3" id="note" > 
             
            <div class="m-2 sm:w-full flex justify-left">
            <button @click="closeModal" class="rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:text-sm">Close</button>
            </div>           
            <div class="mb-2 flex justify-center border-b border-gray-100">  
                <span class="font-semibold text-gray-700 text-lg">Note</span>
            </div>
        <template v-if="editing.id === id">
            <div class="flex justify-center border-b border-gray-100">
                <textarea name="note" id="note" cols="30" rows="3"  v-model="note"
                class=' w-3/4  p-3 rounded-r rounded-l sm:rounded-l-none border border-gray-400  sm:text-sm text-lg text-gray-700'
                >
                </textarea>
            </div>          
        </template>
        <template v-else>
            <div v-if="note !=null" class="flex justify-center border-b border-gray-100 shadow-sm">
                <div 
                class='w-3/4  p-2 rounded-r rounded-l sm:rounded-l-none border border-gray-400  text-lg sm:text-sm text-gray-700'
                >
                 {{note}}
                </div>             
            </div>
        </template>   
      	<div  class="px-5 py-4 flex justify-end">
              <template v-if="(theRecord.user_id == getAuth.user.id) || isFirstLevelUser">
                    <button @click.prevent="edit()" 
                    v-if="editing.id !== id"
                    class="bg-yellow-500 text-white text-sm hover:bg-yellow-700 focus:outline-none mr-1 rounded text-sm py-2 px-3">
                        Edit
                    </button>
                    <template v-if="editing.id === id"> 
                        <button @click.prevent="editing.id = null"
                        class="bg-transparent border border-gray-700 text-sm hover:text-white hover:bg-green-700 focus:outline-none mr-1 rounded text-sm py-2 px-3">
                            Cancel
                        </button>
                        <button @click.prevent="update()"
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
import {mapGetters } from 'vuex'
export default {
    props: ['id','note','table_name','theRecord'],
   data() {
            return {
              editing: {
                    id: null,
                    form: {note: null},
                    errors: null
                },
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
    filteredRecords () {
              
    },
        
    getRoleNames(){
        const rolNameArray = [];
        const allRoleNames = this.getAuth.user.roles;
        allRoleNames.forEach(element => {
            rolNameArray.push(element.name)
        });
        
        return rolNameArray;
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

  methods: {
        edit () {
            this.editing.id = this.id
        },
        
        closeModal(){
            this.$emit('close')
        },

        
        refreshRecords(){
            this.$emit('refreshRecords')
        },


        update() {
        this.editing.form.note = this.note
         axios.post(`/api/datatable/${this.table_name}/updateNote/${this.id}`, this.editing.form).then((response) => {
                this.refreshRecords()
                 this.editing.id = null
                this.editing.form.note = null     
            })
        },

     

    },
   
  
mounted() {
},
    
}

</script>

<style  lang="scss">
    


.modal {
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
