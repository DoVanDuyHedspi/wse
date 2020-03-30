import User from './UserComponent';
import ShowUser from './ShowUserComponent';
import EditUser from './EditUserComponent';
import CreateUser from './CreateUserComponent';

export default [
  {
    path: '/users', component: User, name: 'user.index'
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

]