import { createStore } from 'vuex'
import axios from 'axios'

export default createStore({
  state: {
    posts:[],
    meta: {}
  },
  getters: {
    allPosts: (state) => state.posts,
    allPaginationMeta: (state) => state.meta,
  },
  mutations: { 
    setPosts: (state,posts) => {
      state.posts = posts
    },
    setPaginationMeta: (state,meta) => {
      state.meta = meta
    },
  },
  actions: {
    async fetchPosts({commit},page) {
      const response = await axios.get('/posts',{
        params: {
          page
        }
      })
      console.log(response.data)
      console.log(response.data.meta)
      commit('setPosts', response.data.data)
      commit('setPaginationMeta', response.data.meta)
    },
  },
  modules: {
  }
})
