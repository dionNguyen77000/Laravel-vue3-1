<template>

    <Header> </Header> 
    <SidebarHamburger v-if="!$route.meta.hideNavigation"/>
    <!-- v-if="showSidebarHamburger" -->
    
    <!-- <div class="flex justify-center"> -->

    <router-view />
 
    <!-- </div> -->
    <Footer></Footer>
   
</template>

<script>

export default {
    data() {
        return {
            ourApp: {
                    name: 'GL',
                },
        }
    },
       
    computed: {
    
    },

    // data() {
    //     return {

    //     }
    //     // thecurrentpath = this.$route.path
    //     // this.$route.path ==='/dashboard' ? this.showSidebarHamburger = true : this.showSidebarHamburger = false
    // },

     mounted() {
         
     },
    created() {
        // axios.defaults.headers.common["Authorization"] = "Bearer" + localStorage.getItem("user_token");
    }
   
}
</script>

<style>

nav a  {
    font-weight: bold;
    color:#2c3e50;
    text-decoration: none;
    padding: 10px;
    border-radius: 4px;
}
nav .router-link-active{
    color:white;
    background-color:crimson;
}
</style>