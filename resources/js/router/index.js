import { createWebHistory, createRouter } from "vue-router";
import Home from "../Pages/Home.vue";
import Dashboard from "../Pages/Dashboard.vue";
import Reminder from "../Pages/Reminder.vue";
import Register from "../Pages/auth/register.vue";
import Login from "../Pages/auth/login.vue";
import User_Management from "../Pages/Admin/User_Management.vue";

const routes = [

  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/dashboard",
    name: "Dashboard",
    component: Dashboard,
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

  {
    path: "/login",
    name: "Login",
    component: Login,
  },

  {
    path: "/login",
    name: "Login",
    component: Login,
  },

  {
    path: "/user_management",
    name: "User_Management",
    component: User_Management,
  },


];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;