<template>    
<div class="mb-3 p-3 border border-indigo-600 rounded-md shadow-md">
    <a href="" class="font-bold text-lg">{{post.user.name}}</a> -
    <span class="text-gray-600 text-sm">{{post.howLong}}</span>
    <p class="mb-1 text-xl">{{post.body}}</p>
            <form action="{{ route('posts.destroy',$post) }}" method="post" class="mr-1">
                <button type="submit" class="py-1 px-2 shadow-md rounded-full bg-red-400 text-white text-sm hover:bg-red-700 focus:outline-none active:bg-green-crimson">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
</div>
</template>

<script>
// import LikeButton from './LikeButton.vue'
export default {
    props: [
        'post'
    ],
    components: [
            // LikeButton
    ],
   
}
</script>

<style scoped>
     
</style>