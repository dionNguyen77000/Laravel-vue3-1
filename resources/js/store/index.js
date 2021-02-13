import { createStore } from 'vuex'
import axios from 'axios'

export default createStore({
  state: {
    posts:[]
  },
  getters: {
    allPosts: (state) => state.posts
  },
  mutations: { 
    setPosts: (state,posts) => {
      state.posts = posts
    },
    addPost: (state,post) => {
      state.posts.unshift(post)
    },
    
    removePost: (state,id) => state.posts = state.posts.filter(post=> post.id !== id),

    updatePost: (state, updTodo) => {
      const index = state.todos.findIndex(todo => todo.id ===updTodo.id)
      if (index !== -1){
        state.todos.splice(index,1,updTodo)
      }
    }
  },
  actions: {
    async fetchPosts({commit}) {
      const response = await axios.get('/posts_vue')
      // console.log(response.data)
      commit('setPosts', response.data)
    },
    async addPost({commit}, post_content) {
      await axios.post('/posts_vue',{body: post_content})
      .then((response)=>{
          commit('addPost', response.data)
      });
    },
    async deletePost({commit}, post){
      if (!window.confirm('Are you sure ?')){
        return
      }
      await axios.delete(`posts_vue/${post.id}`)
      .then((response)=>{
        console.log('result of delete', response.data)
        commit('removePost', post.id)
      })
      
    },
  },
  modules: {
  }
})
