<template>

<div class="flex justify-center">

    <div class="w-8/12 bg-white p-6 rounded-lg">
    <h1 class="text-2xl text-gray-900 text-center font-bold  p-4 mb-4">Login</h1>
        <div class="text-center bg-red-700 text-white p-3 text-2lg mb-4" v-if="loginForm.errors['message']">
                <strong>{{ loginForm.errors['message'] }}</strong>
        </div>
        <form action="#"    @submit.prevent="login">
            <div class="mb-4">
                <label for="username" class="sr-only"> Username </label>
                <input type="text" 
                name="username" id="username" placeholder="Your username" 
                class="bg-gray-100 border-2 w-full p-4 rounded-lg"   
                 :class="{ 'border-red-500': loginForm.errors['username'] }"
                v-model="loginForm.fields.username"
                >
                <div class="text-red-500 mt-2 text-sm" v-if="loginForm.errors['username']">
                        <strong>{{ loginForm.errors['username'][0] }}</strong>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="sr-only"> Password </label>
                <input type="password" 
                name="password" id="password" placeholder="Choose a password" 
                class="bg-gray-100 border-2 w-full p-4 rounded-lg"  
                :class="{ 'border-red-500': loginForm.errors['password'] }"
                 v-model="loginForm.fields.password"
                >
                <div class="text-red-500 mt-2 text-sm" v-if="loginForm.errors['password']">
                        <strong>{{ loginForm.errors['password'][0] }}</strong>
                </div>
            </div>
<!-- 
            <div class="mb-4">
               <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class ="mr-2">
                    <label for="remember">Remember me</label>
               </div>
            </div> -->

            <div>
                <button         
                type="submit"
                class="bg-blue-500 text-white px-4 py-3 rounded font-medium w-full"
                >Login</button>
            </div>
        
        </form>

    </div>
</div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
import guest from '../../router/middleware/guest'
import auth from '../../router/middleware/auth'
import redirectIfNotCustomer from '../../router/middleware/redirectIfNotCustomer'

export default {
    name: 'Login',
     middleware: [
        guest,
        //    redirectIfNotCustomer
    ],
    data () {
        return {
            user: window.User,
            loginForm: {
                fields: {
                    username:'',
                    password:''
                },
                errors: []
            },
          
        }
    },

    computed: {
       
    },
    methods: {
         ...mapActions({
             setUpLoginedAuth: 'auth/setUpLoginedAuth'
         }),
         async login(){
            await axios.post('/api/auth/login',this.loginForm.fields)
                .then((response)=>{
                   
                        this.loginForm.id = null
                        this.loginForm.fields.username = ''
                        this.loginForm.fields.password = ''
                        this.loginForm.errors = []

                        // console.log('our Data is', response.data);
                        this.setUpLoginedAuth(response.data.meta.token).then(()=>{
                            this.$router.replace({
                                name: 'Dashboard'
                            })
                        }). catch(()=>{
                            console.log('failed redirection')
                        })

                        // localStorage.setItem("user_token", response.data.meta.token)
                        // this.$router.replace({
                        //     name: 'Dashboard'
                        // })
                    
                })
                .catch((error) => {
                // console.log("🚀error", error)                   
                    if (error.response.status === 401) { 
                        console.log('have error')
                        this.loginForm.errors.message= error.response.data.errors.message[0]
                        // commit('setTheFormError', error.response.data.errors)
                    } else if (error.response.status === 422) {
                        this.loginForm.errors = error.response.data.errors    
                        this.loginForm.errors.message =error.response.data.message                   
                    }

                });
            },
        
    },
    mounted(){
    
    }
   
}
</script>

<style scoped>


</style>