import axios from "axios"
// import store from './index'

export default {
    namespaced: true,
    state: {
        auth: {
            token: null,
            loggedIn: false,
            isFirstLevelUser: false,
            isSecondLevelUser: false,
            isThirdLevelUser: false,
            isFourthLevelUser: false,
            user: {
                token: null,
                loggedIn: false,
                user: [],
            },
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
            // console.log("🚀 ~ file: auth.js ~ line 18 ~ SET_USER ~ authenticatedUser", authenticatedUser)
            state.auth.user = authenticatedUser
        },

        SET_LOGIN (state, rootState) {
            // console.log(rootState)
            if (state.auth.user && state.auth.token) {
                state.auth.loggedIn = true
                state.auth.isCustomer=false
                console.log(state)

                // const rolNameArray = [];
                // const allRoleNames = state.auth.user.roles;
                // allRoleNames.forEach(element => {
                //     rolNameArray.push(element.name)
                // });

                const roleNameArray = _.map(state.auth.user.roles, 'name');

                // console.log('role name is: ' + roleNameArray)
              
                // state.auth.isFirstLevelUser = true
                //set firstLevelusers
                for (var i = 0; i < rootState.firstLevelUsers.length; i++) {
                    console.log(rootState.firstLevelUsers[i])
                    if (roleNameArray.includes(rootState.firstLevelUsers[i])) 
                    {
                        state.auth.isFirstLevelUser = true
                        break;
                    }
                }
                //set secondLevelUsers
                for (var i = 0; i < rootState.secondLevelUsers.length; i++) {
                    console.log(rootState.secondLevelUsers[i])
                    if (roleNameArray.includes(rootState.secondLevelUsers[i])) 
                    {
                        state.auth.isSecondLevelUser = true
                        break;
                    }
                }
                //set thirdLevelUsers
                for (var i = 0; i < rootState.thirdLevelUsers.length; i++) {
                    console.log(rootState.thirdLevelUsers[i])
                    if (roleNameArray.includes(rootState.thirdLevelUsers[i])) 
                    {
                        state.auth.isThirdLevelUser = true
                        break;
                    }
                }
                //set fourthLevelUsers
                for (var i = 0; i < rootState.fourthLevelUsers.length; i++) {
                    console.log(rootState.fourthLevelUsers[i])
                    if (roleNameArray.includes(rootState.fourthLevelUsers[i])) 
                    {
                        state.auth.isFourthLevelUser = true
                        break;
                    }
                }

            } else {
                state.auth.loggedIn = false
                state.auth.isCustomer=false
            }
        },
      
    },

    actions: {
        async setUpLoginedAuth({commit,state,rootState},token){
            // console.log(rootState)
            if(token){
                commit('SET_TOKEN', token)
            }

            if(!state.auth.token){
                return
            }

            try {
                let response = await axios.get('api/auth/me'
                // ,{
                    // headers: {
                    //     'Authorization' : 'Bearer' + token
                    // }
                // }
                )
                commit('SET_USER', response.data.data)
                commit('SET_LOGIN', rootState)

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