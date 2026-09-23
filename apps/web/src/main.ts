import { createApp } from 'vue';
import { Quasar } from 'quasar';
import 'quasar/src/css/index.sass';
import './styles/app.sass';
import App from './App.vue';

createApp(App).use(Quasar, { config: {} }).mount('#app');
