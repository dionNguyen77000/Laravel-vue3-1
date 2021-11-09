<template>

    <form v-if="getAuth.loggedIn" method="post" class="mb-4"  @submit.prevent="post">

        
        <label  class="font-semibold" :for="column"> : </label>
        <select :name="column" :id="column">
            <option :value="index" v-for="option,index in itemOptions" :key="option">
                {{option}}
            </option>
        </select>

        <input type="text">

        <div class="grid justify-items-center">

            <button class="bg-indigo-500 hover:bg-indigo-800 text-white px-4 py-2 rounded ">
                Save
            </button>
        </div>
    </form>

    
</template>

<script>
    import {mapGetters, mapActions} from 'vuex'
    
    export default {
        name: "PostForm",
        props: ['itemOptions'],    

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
                await axios.post('api/posts',this.postForm.fields)
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
