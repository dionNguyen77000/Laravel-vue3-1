import { createWebHistory, createRouter } from "vue-router";
import store from '../store'

//router components
import Home from "../Pages/Home.vue";
import Dashboard from "../Pages/Dashboard.vue";
import Reminder from "../Pages/Reminder.vue";
import Register from "../Pages/auth/register.vue";
import Login from "../Pages/auth/login.vue";
import User_Management from "../Pages/Admin/User_Management.vue";

//Midelware 
import auth from './middleware/auth'
import guest from './middleware/guest'
import redirectIfNotCustomer from './middleware/redirectIfNotCustomer'
import middlewarePipeline from "./kernel/middlewarePipeline";

const routes = [

  {
    path: "/",
    name: "Home",
    component: Home,
    
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
    // meta: {
    //   middleware: [
    //     guest
    //   ]
    // },
  },

  {
    path: "/dashboard",
    name: "Dashboard",
    component: Dashboard,

    // meta: {
    //   middleware: [
    //     auth
    //   ]
    // },

    children: [
      { 
        path: '', 
        component: Home 
      },
      {
        path: "/dashboard/user_management",
        name: "User_Management",
        component: User_Management,
        // meta: {
        //   middleware: [
        //     auth, redirectIfNotCustomer
        //   ]
        // },
      },
    ],
   
    // beforeEnter: (to, from, next) => {
    //   if(!store.getters['auth/getAuth'].loggedIn){
    //     return next({
    //       name: 'Login'
    //     })
    //   }
    //   next()
    // }
  },
  {
    path: "/reminder",
    name: "Reminder",
    component: Reminder,
    meta: { hideNavigation: true }
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
  },



];


const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next)=> {
  
  let middleware = to.matched.map((matched) => {
    return matched.components.default.middleware
  })

  //remove undefined middlewares 
  .filter((middleware) => {
    return middleware !== undefined
  })

  //remove the first unnecessary 1st level 
  .flat()


  console.log('middleware is ', middleware)

  if (!middleware.length){
    return next()
  }

  // if(!to.meta.middleware) {
  //   return next()
  // }

  // const middleware = to.meta.middleware

  const context = {
    to,
    from,
    next,
    router,
    store
  }

  console.log('middleware is',middleware[0])
  return middleware[0]({ 
    ...context, 
    next: middlewarePipeline(context, middleware, 1) 
  })
  // next()
})

export default router;