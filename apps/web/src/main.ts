import { createApp } from 'vue';
import { Quasar } from 'quasar';
import 'quasar/src/css/index.sass';
import '@quasar/extras/material-icons/material-icons.css';
import './styles/app.sass';
import App from './App.vue';

createApp(App).use(Quasar, { config: {} }).mount('#app');
