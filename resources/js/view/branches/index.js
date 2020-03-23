import Branches from './ListBranchesComponent';

export default [
  {
    path: '/branches', component: Branches, name: 'branches.index', meta: {
      requiredRoles: ['manager', 'group-manager', 'direct-manager', 'employee']
    }
  },
]