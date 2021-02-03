<template>
    <form action="" method="post" class="mb-4"  @submit.prevent="post">
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
</template>

<script>
    // import eventHub from '../event'
    // import Post from './Post.vue'
    // import postform from './PostForm.vue'

    export default {
        data (){
            return {
               body: null
            }
        },
        // components: [
        //     Post, postform
        // ],
        methods: {
            post(){
            alert(this.body);
            axios
            .post('/posts_vue',{
                body: this.body
            }).then((response)=>{
                //emit event
                this.$emit('addPost');
                // eventHub.$emit('post-added', response.data)
                console.log('result is',response.data)
                this.body = null
            });
           
        }
        },
        
    }
</script>
