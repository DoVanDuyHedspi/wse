import Permission from './PermissionComponent';
import Role from './RoleComponent';

export default [
  {
    path: '/permission', component: Permission, name: 'permission.index', meta: {
      permission: 'view-permissions',
    }
  },
  {
    path: '/role', component: Role, name: 'role.index', meta: {
      permission: 'view-roles',
    }
  },
]