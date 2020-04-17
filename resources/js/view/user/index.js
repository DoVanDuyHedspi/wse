import User from './UserComponent';
import ShowUser from './ShowUserComponent';
import EditUser from './EditUserComponent';
import CreateUser from './CreateUserComponent';
import NoPermission from './NoPermissionComponent';
import NotFound404 from './NotFound404Component';

export default [
  {
    path: '/users', component: User, name: 'user.index', meta: {
      permission: 'view-users',
    }
  },
  {
    path: '/users/create', component: CreateUser, name: 'user.create'
  },
  {
    path: '/users/:id', component: ShowUser, name: 'user.show'
  },
  {
    path: '/users/edit/:id', component: EditUser, name: 'user.edit'
  },
  {
    path: '/no_permission', component: NoPermission, name: 'no_permission'
  },
  {
    path: '/not_found', component: NotFound404, name: '404'
  },
]