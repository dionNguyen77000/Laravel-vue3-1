import axios from "axios"

export default {
    namespaced: true,
    state: {
        auth: {
            token: null,
            loggedIn: false,
            user: null
          },
    },
    getters: {
        getAuth: (state) => state.auth,
    },
    mutations: {
        SET_TOKEN (state,token) {
            state.auth.token = token
        },

        SET_USER (state, authenticatedUser) {
            console.log("🚀 ~ file: auth.js ~ line 18 ~ SET_USER ~ authenticatedUser", authenticatedUser)
            state.auth.user = authenticatedUser
        },

        SET_LOGIN (state) {
            if (state.auth.user && state.auth.token) {
                state.auth.loggedIn = true
            } else {
                state.auth.loggedIn = false
            }
        }
    },

    actions: {
        async setUpLoginedAuth({commit,state},token){
            if(token){
                commit('SET_TOKEN', token)
            }

            if(!state.auth.token){
                return
            }

            try {
                let response = await axios.get('api/auth/me',{
                    // headers: {
                    //     'Authorization' : 'Bearer' + token
                    // }
                })
                commit('SET_USER', response.data.data)
                commit('SET_LOGIN')

            } catch (e) {
                commit('SET_TOKEN', null)
                commit('SET_USER', null)
                commit('SET_LOGIN')
            }
        },

        logOut({commit}) {
           return  axios.post('api/auth/logout').then(()=>{
            commit('SET_TOKEN', null)
            commit('SET_USER', null)
            commit('SET_LOGIN')
           }) 
        }
    }
}