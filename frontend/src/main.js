import router from './router/index';
import { createApp } from 'vue'
import { createPinia } from 'pinia';
import echo from './echo'

import './main.css'
import '@vueform/slider/themes/default.css'
import App from './App.vue'

//Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';

//Icon
import 'remixicon/fonts/remixicon.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'material-icons/iconfont/material-icons.css';

const pinia = createPinia();
const app = createApp(App);
app.use(pinia);
app.use(router);

window.Echo = echo

app.mount('#app')
