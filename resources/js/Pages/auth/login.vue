<template>

<div class="flex justify-center">

    <div class="w-8/12 bg-white p-6 rounded-lg">
    <h1 class="text-2xl text-gray-900 text-center font-bold  p-4 mb-4">Login</h1>
        <div class="text-center bg-red-700 text-white p-3 text-2lg mb-4">
        </div>
        <form action="" method="post">
            <div class="mb-4">
                <label for="username" class="sr-only"> Username </label>
                <input type="text" name="username" id="username" placeholder="Your username" class="bg-gray-100 border-2 w-full p-4 rounded-lg"   value="">
                    <div class="text-red-500 mt-2 text-sm"></div>
            </div>

            <div class="mb-4">
                <label for="password" class="sr-only"> Password </label>
                <input type="password" name="password" id="password" placeholder="Choose a password" class="bg-gray-100 border-2 w-full p-4 rounded-lg"  value="">
                    <div class="text-red-500 mt-2 text-sm"></div>
            </div>

            <div class="mb-4">
               <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class ="mr-2">
                    <label for="remember">Remember me</label>
               </div>
            </div>

            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-3 rounded font-medium w-full">Login</button>
            </div>
        
        </form>

    </div>
</div>
</template>

<script>

export default {
    name: 'Register',
   

    computed: {
       
    },
    methods: {
    },
    components:{
    },
  
    mounted() {
    
    }
}
</script>

<style scoped>


</style>