
require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
import routers from './routes.js'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI);
Vue.use(VueRouter);
Vue.use(routers);

const router = new VueRouter({
  mode: 'hash',
  routes: routers
})

Vue.component('app', require('./components/AppComponent.vue').default);
Vue.component('pagination', require('laravel-vue-pagination'));

const app = new Vue({
  el: '#app',
  data: {
    user: window.__user__
  },
  router
});
