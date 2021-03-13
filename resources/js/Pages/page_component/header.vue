<template>
  <nav class="w-full p-6 bg-yellow-600 sticky top-0 z-40 flex justify-between">
     

    
        <ul class="flex items-center">
             <p class="font-semibold text-3xl text-blue-400 pl-4">LOGO</p>
            <router-link class="mr-4" to="/"> Home </router-link>
            <router-link class="mr-4" :to="{name:'Dashboard'}">Dashboard</router-link>
            <router-link class="mr-4" :to="{name:'Reminder'}">Reminder</router-link>
        </ul>
        <ul class="flex items-center">
        <!-- @if (auth()->user()) -->
        <li>
          Hi  
        </li>
        <li>
            <form  method="post" class="p-3 inline">
            <!-- @csrf -->
            <button type="submit">Logout</button>
            </form>
        </li>
        <li>
           <a href="">Admin</a>
        </li>
        <router-link class="mr-4" :to="{name:'Login'}">Login</router-link>
        <router-link class="mr-4" :to="{name:'Register'}">Register</router-link>
         <img src="https://a7sas.net/wp-content/uploads/2019/07/4060.jpeg" class="w-12 h-12 rounded-full shadow-lg" @click="dropDownOpen = !dropDownOpen">

       
        </ul>
       
        <!-- @endif -->
</nav>
</template>
