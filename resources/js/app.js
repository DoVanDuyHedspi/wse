
require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
import routers from './routes.js'
import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/vi';
import 'element-ui/lib/theme-chalk/index.css';
import usersStore from './store/usersStore';
import Print from 'vue-print-nb'
// import { CalendarView, CalendarViewHeader } from 'vue-simple-calendar'

Vue.use(ElementUI, { locale });
Vue.use(VueRouter);
Vue.use(routers);
Vue.use(Print);

const router = new VueRouter({
  mode: 'hash',
  routes: routers
})

router.beforeEach((to, from, next) => {
  let user = window.__user__
  let l = router.resolve(to.path);
  if (l.resolved.matched.length <= 0 && to.path != '/') {
    next({
      path: '/not_found'
    })
  };
  if (to.meta.permission) {
    if (user.all_permissions.includes(to.meta.permission)) {
      next()
    } else {
      next({
        path: '/no_permission'
      })
    }
  } else {
    next()
  }
})

Vue.component('app', require('./components/AppComponent.vue').default);
Vue.component('pagination', require('laravel-vue-pagination'));

const app = new Vue({
  el: '#app',
  data: {
    user: window.__user__
  },
  router,
  store: usersStore
});
