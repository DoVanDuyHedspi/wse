import Groups from './SettingOrganizationComponent';

export default [
  {
    path: '/organization', component: Groups, name: 'groups.index', meta: {
      permission: 'view-organization',
    }
  },
]