import IndexOtherLeaves from './IndexOtherRequestComponent';
import NewOtherRequest from './NewOtherRequestComponent';
import EditOtherRequest from './EditOtherRequestComponent';
import IndexRequestOT from './IndexRequestOTComponent';
import NewRequestOT from './NewRequestOTComponent';
import EditRequestOT from './EditRequestOTComponent';
import UsersOtherRequest from './UsersOtherRequestComponent';
import UsersRequestsOT from './UsersRequestOTComponent';
import IndexRequestCheckCamera from './IndexRequestCheckCameraComponent';
import NewRequestCheckCamera from './NewRequestCheckCameraComponent';
import EditRequestCheckCamera from './EditRequestCheckCameraComponent';
import UsersRequestCheckCamera from './UsersRequestCheckCameraComponent';
import CheckCamera from './CheckCameraComponent';
import ConfirmWorked from './ConfirmWorkedComponent';
import IndexRequestLeave from './IndexRequestLeave';
import NewRequestLeave from './NewRequestLeave';
import EditRequestLeave from './EditRequestLeave';
import UsersRequestLeave from './UsersRequestLeave';

export default [
  {
    path: '/other_request', component: IndexOtherLeaves, name: 'other_request.index'
  },
  {
    path: '/other_request/new', component: NewOtherRequest, name: 'other_request.new'
  },
  {
    path: '/other_request/edit/:id', component: EditOtherRequest, name: 'other_request.edit'
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
    path: '/users_requests/other', component: UsersOtherRequest, name: 'users_requests_other.index', meta: {
      permission: 'check-requests',
    }
  },
  {
    path: '/users_requests/confirm_worked', component: ConfirmWorked, name: 'confirm_worked', meta: {
      permission: 'approve-requests',
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
    path: '/users_requests/request_check_camera', component: UsersRequestCheckCamera, name: 'users_request_check_camera.index', meta: {
      permission: 'approve-requests',
    }
  },
  {
    path: '/users_requests/check_camera', component: CheckCamera, name: 'check_camera', meta: {
      permission: 'approve-requests',
    }
  },
  {
    path: '/request_leave', component: IndexRequestLeave, name: 'request_leave.index'
  },
  {
    path: '/request_leave/new', component: NewRequestLeave, name: 'request_leave.new'
  },
  {
    path: '/request_leave/edit/:id', component: EditRequestLeave, name: 'request_leave.edit'
  },
  {
    path: '/users_requests/leave', component: UsersRequestLeave, name: 'users_requests_leave.index', meta: {
      permission: 'check-requests',
    }
  },
]