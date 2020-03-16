
require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
import routers from './routes.js'
import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/vi'
import 'element-ui/lib/theme-chalk/index.css';

Vue.use(ElementUI, { locale });
Vue.use(VueRouter);
Vue.use(routers);

const router = new VueRouter({
  mode: 'hash',
  routes: routers
})

router.beforeEach((to, from, next) => {
  let user = window.__user__
  if (to.meta.requiredRoles.includes(user.slug)) {
    next()
  } else {
    alert('You don\'t have permission to access this page.')
    next({
      path: '/'
    })
  }
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
