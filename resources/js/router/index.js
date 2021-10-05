import { createWebHistory, createRouter } from "vue-router";
import store from '../store'

//router components
import Home from "../Pages/Home.vue";
import DashboardHome from "../Pages/Home.vue";
import Dashboard from "../Pages/Dashboard.vue";
import Reminder from "../Pages/Reminder.vue";
import Register from "../Pages/auth/register.vue";
import Login from "../Pages/auth/login.vue";
import User_Management from "../Pages/Admin/User_Management.vue";
import Role from "../Pages/Admin/Role.vue";
import Permission from "../Pages/Admin/Permission.vue";



//router component Stock Setup
import Category from "../Pages/stock/category.vue";
import Supplier from "../Pages/stock/supplier.vue";
import Unit from "../Pages/stock/unit.vue";
import Goods_Material from "../Pages/stock/goods_material.vue";
import Intermediate_Product from "../Pages/stock/intermediate_product.vue";
import Daily_Emp_Work from "../Pages/stock/daily_emp_work.vue";

//router component Order Setup
import Orders_To_Suppliers from "../Pages/stock/orders_to_suppliers.vue";
import Invoices_From_Suppliers from "../Pages/stock/invoices_from_suppliers.vue";


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
        path: "/user_management",
        name: "User_Management",
        component: User_Management,
        // meta: {
        //   middleware: [
        //     auth, redirectIfNotCustomer
        //   ]
        // },
      },
      {
        path: "/role",
        name: "Role",
        component: Role,
      },

      {
        path: "/permission",
        name: "Permission",
        component: Permission,
      },

      {
        path: "/supplier",
        name: "Supplier",
        component: Supplier,
      },

      {
        path: "/category",
        name: "Category",
        component: Category,
      },
      {
        path: "/unit",
        name: "Unit",
        component: Unit,
      },
      {
        path: "/goods_material",
        name: "Goods_Material",
        component: Goods_Material,
      },
      {
        path: "/intermediate_product",
        name: "Intermediate_Product",
        component: Intermediate_Product,
      },
      {
        path: "/daily_emp_work",
        name: "Daily_Emp_Work",
        component: Daily_Emp_Work,
      },
      {
        path: "/orders_to_suppliers",
        name: "Orders_To_Suppliers",
        component: Orders_To_Suppliers,
      },
      {
        path: "/invoices_from_suppliers",
        name: "Invoices_From_Suppliers",
        component: Invoices_From_Suppliers,
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
    // console.log('our middleware',matched.components.default.middleware);
    return matched.components.default.middleware
  })
  //remove undefined middlewares 
  .filter((middleware) => {
    return middleware !== undefined
  })

  //remove the first unnecessary 1st level 
  .flat()


  // console.log('middleware is ', middleware)

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

  // console.log('middleware is',middleware[0])

  return middleware[0]({ 
    ...context, 
    next: middlewarePipeline(context, middleware, 1) 
  })
  // next()
})

export default router;