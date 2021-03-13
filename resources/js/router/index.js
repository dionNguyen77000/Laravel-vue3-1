import { createWebHistory, createRouter } from "vue-router";
import Home from "../Pages/Home.vue";
import Dashboard from "../Pages/Dashboard.vue";
import Reminder from "../Pages/Reminder.vue";
import Register from "../Pages/auth/register.vue";
import Login from "../Pages/auth/login.vue";

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
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;