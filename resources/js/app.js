require('./bootstrap');

import {createApp} from 'vue'
import App from './App.vue'
import store from './store'

const app = createApp(App).use(store)
app.config.globalProperties.foo = 'bar'
app.config.globalProperties.user = window.User
// app.coprototype.$user = window.User
app.mount('#app')

// add global variable

