<template>
<div class="flex justify-center">
    <div class="w-8/12 bg-white p-6 roudned-lg">
    <PostForm/>
    <Post v-for="post in allPosts" :post="post" :key="post.id"> </Post>
    </div>
</div>
</template>

<script>
import PostForm from './components/PostForm.vue'
import Post from './components/Post.vue'
import {mapGetters, mapActions} from 'vuex'
export default {
    name: 'App',
    data(){
        return {
           posts:[],
            user: window.User
        }
    },  
    computed: mapGetters(['allPosts']),
    methods: {
        ...mapActions(['fetchPosts', 'deletePost', "updatePost"]),
    },
    components:{
        PostForm, Post,
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