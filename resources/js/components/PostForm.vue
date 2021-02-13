<template>
    
    <form  v-if = "user != null" action="" method="post" class="mb-4"  @submit.prevent="post">
        <div class="mb-4">
            <label for="body" class="sr-only">Body</label>
            <textarea v-model="body" name="body" id="body" cols="30" rows="4" class="bg-gray-100 border-2 w-full p-4 rounded-lg @error('body') border-red-500 @enderror" placeholder="Post something!"></textarea>

                <div class="text-red-500 mt-2 text-sm">
                </div>
        </div>
        <div>
            <button class="bg-blue-500 text-white px-4 py-2 rounded font-medium">
                Post
            </button>
        </div>
    </form>

     <h1 v-else class=" text-4xl text-red-700 text-center mb-8"> You Need to Login to post</h1>
    
</template>

<script>
    import { mapActions, mapGetters } from "vuex";

    export default {
        name: "PostForm",
        data (){
            return {
                body: null,
                user: window.User,
            }
        },
    
        // components: [
        //     Post, postform
        // ],
        methods: {
            ...mapActions(['addPost']),
            post(){
                this.addPost(this.body);
                this.body = null
            }
        },
        mounted(){
        // console.log('post form user is ',this.user) // 'bar'
        }
    }
</script>
