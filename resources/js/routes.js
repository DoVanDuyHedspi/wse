// import Vue from 'vue'
// import VueRouter from 'vue-router'

// Vue.use(VueRouter)

// const router = new VueRouter({
//   mode: 'hash',
//   linkActiveClass: 'active',
//   routes: [
//     {
//       path: '/user',
//       name: 'user', 
//       component: require('./components/UserComponent.vue').default,
//     }
//   ]
// })
// export default router

import userRoutes from './view/user';
import permissionRoutes from './view/permissions';
import branchRoutes from './view/branches';
import groupRoutes from './view/groups';

export default [
  ...userRoutes,
  ...permissionRoutes, 
  ...branchRoutes,
  ...groupRoutes,
]