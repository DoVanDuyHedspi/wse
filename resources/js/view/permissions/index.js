import Permission from './PermissionComponent';
import Role from './RoleComponent';

export default [
  {
    path: '/permission', component: Permission, name: 'permission.index'
  },
  {
    path: '/role', component: Role, name: 'role.index'
  },
]