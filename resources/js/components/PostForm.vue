<template>
 
    <form method="post" class="mb-4"  @submit.prevent="post">
        <div class="mb-1">
            <label for="body" class="sr-only">Body</label>
            <textarea 
            v-model="postForm.fields.body" name="body" id="body" cols="30" rows="4" 
            class="bg-gray-100 border-2 w-full p-4 rounded-lg shadow-md" 
            :class="{ 'border-3 border-red-700': postForm.errors.body}" 
            placeholder="Remind something ...">
            </textarea>
            <div v-if="postForm.fields.body.length == 0">
                <p class="text-red-700 font-bold" v-if="postForm.errors.body">{{postForm.errors.body}}</p>
            </div>
        </div>
        <div class="grid justify-items-center">
        <button class="bg-indigo-500 hover:bg-indigo-800 text-white px-4 py-2 rounded ">
            Post
        </button>
        </div>
    </form>

     <h1 v-else class=" text-4xl text-red-700 text-center mb-8"> You Need to Login to post</h1>
    
</template>

<script>
    import {mapGetters, mapActions} from 'vuex'
    export default {
        name: "PostForm",
        data (){
            return {
                body: null,
                user: window.User,
                postForm: {
                    id: null,
                    fields: {'body': ''},
                    errors: []
                },
            }
        },
       computed: {
            ...mapGetters({
                getAuth: 'auth/getAuth'
            }),
        },
        methods: {
            ...mapActions(['fetchPosts']),
            async post(){
                await axios.post('/posts',this.postForm.fields)
                    .then((response)=>{
                        this.fetchPosts()
                        .then((response) => {
                            // commit('resetTheForm')
                            this.postForm.id = null
                            this.postForm.fields.body = ''
                            this.postForm.errors = []
                        })
                    })
                    .catch((error) => {
                        if (error.response.status === 422) { 
                            this.postForm.errors.body = error.response.data.errors.body[0]
                            // commit('setTheFormError', error.response.data.errors)
                        }
                    });
            },
        },
        mounted(){
         
        }
    }
</script>
