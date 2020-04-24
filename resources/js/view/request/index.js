import IndexRequestLeaves from './IndexRequestLeavesComponent';
import NewRequestLeaves from './NewRequestLeavesComponent';
import EditRequestLeaves from './EditRequestLeavesComponent';
import IndexRequestOT from './IndexRequestOTComponent';
import NewRequestOT from './NewRequestOTComponent';
import EditRequestOT from './EditRequestOTComponent';
import UsersRequestsLeaves from './UsersRequestLeavesComponent';
import UsersRequestsOT from './UsersRequestOTComponent';
import IndexRequestCheckCamera from './IndexRequestCheckCameraComponent';
import NewRequestCheckCamera from './NewRequestCheckCameraComponent';
import EditRequestCheckCamera from './EditRequestCheckCameraComponent';
import UsersRequestCheckCamera from './UsersRequestCheckCameraComponent';
export default [
  {
    path: '/request_leaves', component: IndexRequestLeaves, name: 'request_leaves.index'
  },
  {
    path: '/request_leaves/new', component: NewRequestLeaves, name: 'request_leaves.new'
  },
  {
    path: '/request_leaves/edit/:id', component: EditRequestLeaves, name: 'request_leaves.edit'
  },
  {
    path: '/request_ot', component: IndexRequestOT, name: 'request_ot.index'
  },
  {
    path: '/request_ot/new', component: NewRequestOT, name: 'request_ot.new'
  },
  {
    path: '/request_ot/edit/:id', component: EditRequestOT, name: 'request_ot.edit'
  },
  {
    path: '/users_requests/ot_remote', component: UsersRequestsOT, name: 'users_requests_ot.index', meta: {
      permission: 'check-requests',
    }
  },
  {
    path: '/users_requests/leaves', component: UsersRequestsLeaves, name: 'users_requests_leaves.index', meta: {
      permission: 'check-requests',
    }
  },
  {
    path: '/request_check_camera/', component: IndexRequestCheckCamera, name: 'request_check_camera.index'
  },
  {
    path: '/request_check_camera/new', component: NewRequestCheckCamera, name: 'request_check_camera.new'
  },
  {
    path: '/request_check_camera/edit/:id', component: EditRequestCheckCamera, name: 'request_check_camera.eidt'
  },
  {
    path: '/users_requests/check_camera', component: UsersRequestCheckCamera, name: 'users_request_check_camera.index'
  },
]