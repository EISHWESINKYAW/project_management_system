import './bootstrap';

import { createApp } from 'vue'
import App from './App.vue'
import router from './router';
import 'vue3-toastify/dist/index.css';

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

import { registerComponents } from './components';

const app = createApp(App);

app.component('VueDatePicker', VueDatePicker);

registerComponents(app);
app.use(router);
app.mount('#app')
