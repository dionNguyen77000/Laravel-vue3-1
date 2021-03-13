<template>
<div class="flex justify-center">
    <div class="w-11/12 bg-white p-6 roudned-lg">
    <!-- All Posts {{posts}} -->
    <PostForm/>
    <Pagination :meta="allPaginationMeta" v-on:pagination="fetchPosts"/>
    <Post v-for="post in allPosts" :post="post" :key="post.id"> </Post>
    <Pagination :meta="allPaginationMeta" v-on:pagination="fetchPosts"/>
    </div>
</div>
</template>

<script>


import PostForm from '../components/PostForm.vue'
import Post from '../components/Post.vue'
import Pagination from '../components/pagination/Pag.vue'
import {mapGetters, mapActions} from 'vuex'
export default {
    name: 'App',
    data(){
        return {
            user: window.User,
        }
    },

    computed: {
         posts(){
            //  return this.$store.state.posts
         },
        ...mapGetters([
        'allPosts', 'allPaginationMeta',
        ]),
    },
    methods: {
        ...mapActions(['fetchPosts']),
    },
    components:{
        PostForm, Post, Pagination
    },
  
    mounted() {
        
         this.fetchPosts()
            // this.$http.get('/posts').then((response) => {                
            // });
            // eventHub.$on('post-added', this.addPost)

            // axios
            // .get('/posts_vue')
            // .then(response => {
               
            //     // Echo.private('posts').listen('PostWasCreated', (e)=>{
            //     //     console.log(e);
            //     //      console.log('in here'); 
            //     // });
            //     //  console.log(response.data);
                
            //     this.posts= response.data;
            //     // console.log(this.posts);
            // })
        }
}
</script>

<style scoped>


</style>