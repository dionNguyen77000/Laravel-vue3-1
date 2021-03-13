import { createStore } from 'vuex'
import axios from 'axios'

export default createStore({
  state: {
    sideBarOpen: true,
    posts:[],
    meta: {}
  },
  getters: {
    sideBarOpen: state => {
      return state.sideBarOpen
    },
    allPosts: (state) => state.posts,
    allPaginationMeta: (state) => state.meta,
  },
  mutations: { 
    toggleSidebar (state) {
      state.sideBarOpen = !state.sideBarOpen
      console.log("🚀 ~ file: index.js ~ line 20 ~ toggleSidebar ~ state.sideBarOpen ", state.sideBarOpen )
      
    },
    setPosts: (state,posts) => {
      state.posts = posts
    },
    setPaginationMeta: (state,meta) => {
      state.meta = meta
    },
  },
  actions: {
    toggleSidebar(context) {
      context.commit('toggleSidebar')
    },
    async fetchPosts({commit},page) {
      const response = await axios.get('/api/posts',{
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
