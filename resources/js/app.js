
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('vue-multiselect/dist/vue-multiselect.min.css');

import Vue from 'vue';
import router from './services/router';
import store from '../js/services/store';

import globalLayout from '../js/components/global/globalLayout.vue';
import globalInput from '../js/components/global/globalInput.vue'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('index-page', require('./pages/IndexPage/index.vue').default);
Vue.component('global-layout', globalLayout);
Vue.component('global-input', globalInput);

Vue.prototype.$bus = new Vue({});

new Vue({
    store,
    router
  }).$mount('#app')

