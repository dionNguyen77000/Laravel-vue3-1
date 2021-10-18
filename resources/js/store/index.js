import { createStore } from 'vuex'
import axios from 'axios'
import auth from './auth'

export default createStore({
  state: {
     // Level of Users
     firstLevelUsers : ['Admin' ],
     secondLevelUsers : ['Admin' , 'Manager'],
     thirdLevelUsers : ['Admin' , 'Manager', 'Assistant Manager','Supervisor', 
                          'Shif_Supervisor','Chef'],
     fourthLevelUsers : [
       'Admin' , 'Manager', 'Assistant Manager','Supervisor', 'Shif_Supervisor', 'Chef',
       'Stir_Frier','Deep_Frier','Rice_Frier','Veg_Picker','Runner','Server',
       'Driver'
      ],
      sideBarOpen: true,
      posts:[],
      meta: {}
  },

  getters: {
    
    
    sideBarOpen: state => {
      return state.sideBarOpen
    },
    firstLevelUsers: (state) => state.firstLevelUsers,
    secondLevelUsers: (state) => state.secondLevelUsers,
    thirdLevelUsers: (state) => state.thirdLevelUsers,
    fourthLevelUsers: (state) => state.fourthLevelUsers,
    allPosts: (state) => state.posts,
    allPaginationMeta: (state) => state.meta,
   
  },
  mutations: { 
  
    toggleSidebar (state) {
      state.sideBarOpen = !state.sideBarOpen
      // console.log("🚀 ~ file: index.js ~ line 20 ~ toggleSidebar ~ state.sideBarOpen ", state.sideBarOpen )
      
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
      console.log(response)
      // console.log(response.data.meta)
      commit('setPosts', response.data.data)
      commit('setPaginationMeta', response.data.meta)
    },
    
  },
  modules: {
    auth
  }
})
