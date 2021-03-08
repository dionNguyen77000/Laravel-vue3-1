<template>    
<div class="mb-3 p-3 border border-indigo-600 rounded-md shadow-md">
    <a href="" class="font-bold text-lg">{{post.user.name}}</a> -
    <span class="text-gray-600 text-sm">{{post.howLong}}</span>
    <template v-if="editForm.id === post.id">
        <div>
            <form action="#">
                <textarea autoFocus :id='post.id'  v-model="editForm.fields.body" class="p-2 resize border border-grey-800 rounded-md w-full" :class="{ 'border-3 border-red-700': editForm.errors.body }" >  
                </textarea>
            </form>
            <div v-if="editForm.fields.body.length == 0">
                <p class="mb-1 text-red-700 font-bold" v-if="editForm.errors.body">{{editForm.errors.body}}</p>
            </div>
        </div>
         <button 
        class="bg-transparent border border-gray-600 mr-1 p-2  shadow-md rounded-full  text-grey text-sm hover:bg-green-700 hover:text-white focus:outline-none"
        @click="editForm.id = null"
         >
         Cancel
         </button>
         
        <button  
        v-if="editForm.id === post.id" 
        class=" mr-1 py-1 px-3 shadow-md rounded-full bg-blue-400 text-white text-sm hover:bg-red-700 focus:outline-none"
        @click="update"
        >
        Save
        </button>
    </template>
    <template v-else>
        <p class="mb-1 text-xl">{{post.body}}</p>
         <button  
        v-if="user != null && user.id === post.user.id && editForm.id !== post.id" 
        class=" mr-1 py-1 px-4 shadow-md rounded-full bg-yellow-500 text-white text-sm hover:bg-green-700 focus:outline-none"
        @click="edit(post)"
        >
        Edit
        </button>
        <button  
        v-if="user != null && user.id === post.user.id " 
        class=" mr-1 py-1 px-2 shadow-md rounded-full bg-red-400 text-white text-sm hover:bg-red-700 focus:outline-none"
        @click="deletePost(post)"
        >
        Delete
        </button>
    </template>
</div>
</template>

<script>
// import LikeButton from './LikeButton.vue'
import {mapActions} from 'vuex'
import axios from 'axios'

export default {
    
    name: 'Post',
    
    props: [
        'post'
    ],
    
    data () {
        return {
            column: 'body',
            user: window.User,
            editForm: {
                id: null,
                fields: {'body': ''},
                errors: []
            },
        }
    },
    computed: {
        // ...mapGetters(['editForm',]),
    },
  
    components: [
            // LikeButton
    ],

     methods: {
        ...mapActions(['fetchPosts']),
        edit (post) {
            this.editForm.id=post.id
            this.editForm.fields.body = post.body
            this.editForm.errors = []
            // this.editForm.fileds = _.pick(post, ['body'])
            // this.editing.id = post.id
        },

        async update() {
            await axios.patch(`/posts/${this.editForm.id}`,this.editForm.fields)
            .then((response) => {
                this.fetchPosts().then(() => {
                        // commit('resetTheForm')
                        this.editForm.id = null
                        this.editForm.fields.body = ''
                        this.editForm.errors = []
                })
            }).catch((error) => {
                if (error.response.status === 422) {
                     this.editForm.errors.body = error.response.data.errors.body[0]
                } 
            })
        },
        
        async deletePost(post){
            if (!window.confirm('Are you sure ?')){
                return
            }
            await axios.delete(`posts/${post.id}`)
            .then((response)=>{
                this.fetchPosts()
            })
        },
    },
    mounted(){
   
    }
   
}
</script>

<style scoped>
     
</style>