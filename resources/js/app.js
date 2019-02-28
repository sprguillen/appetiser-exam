
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import BootstrapVue from 'bootstrap-vue';
import Toasted from 'vue-toasted';
import axios from 'axios';
import { store } from './store';

const options = {
    position: 'top-center',
    duration: 5000
};

Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(BootstrapVue);
Vue.use(Toasted, options);

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.component('table-component', require('./components/TableComponent.vue').default);

const router = new VueRouter({ mode: 'history' });

const app = new Vue(
    Vue.util.extend({ router, store })
).$mount('#app');
