require('./bootstrap');

import {createApp} from 'vue'
import App from './App.vue'
import App_Stock from './App_Stock.vue'
import store from './store'
import router from './router'

import Header from './Pages/page_component/header.vue'
import Footer from './Pages/page_component/footer.vue'
import Sidebar from './Pages/page_component/sidebar.vue'
import Navbar from './Pages/page_component/Navbar.vue'
import SidebarHamburger from './components/SidebarHamburger.vue'



// const app = createApp(App)
const app = createApp(App_Stock)

// register global component
app.component('Header',Header)
app.component('Navbar',Navbar)
app.component('Footer',Footer)
app.component('Sidebar',Sidebar)
app.component('SidebarHamburger',SidebarHamburger)

// add plugins 
app.use(store)
app.use(router)
app.config.globalProperties.user = window.User
app.mount('#app')





