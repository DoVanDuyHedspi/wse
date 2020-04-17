import Groups from './GroupComponent';

export default [
  {
    path: '/organization', component: Groups, name: 'groups.index', meta: {
      permission: 'view-organization',
    }
  },
]