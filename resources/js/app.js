import './bootstrap';
import { createApp } from 'vue';
import Swal from 'sweetalert2';

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
import Register from './components/Register.vue';
import Dashboard from './components/Dashboard.vue';
app.component('example-component', ExampleComponent);
app.component('register-form', Register);
app.component('dashboard', Dashboard);
app.config.globalProperties.$swal = Swal;
app.mount('#app');
