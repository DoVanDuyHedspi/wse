import User from './UserComponent';
import ShowUser from './ShowUserComponent';
import EditUser from './EditUserComponent';

export default [
  {
    path: '/users', component: User, name: 'user.index', meta: {
      requiredRoles: ['manager', 'group-manager', 'direct-manager']
    }
  },
  {
    path: '/users/:id', component: ShowUser, name: 'user.show', meta: {
      requiredRoles: ['manager', 'group-manager', 'direct-manager', 'employee']
    }
  },
  {
    path: '/users/edit/:id', component: EditUser, name: 'user.edit', meta: {
      requiredRoles: ['manager', 'group-manager', 'direct-manager', 'employee']
    }
  },
]