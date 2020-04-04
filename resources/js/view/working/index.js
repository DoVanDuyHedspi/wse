import TimeSheets from './TimeSheetsComponent';
import ManageTimeSheets from './ManageTimesheetsComponent';

export default [
  {
    path: '/user_timesheets', component: TimeSheets, name: 'working.timesheets'
  },
  {
    path: '/manage_timesheets', component: ManageTimeSheets, name: 'working.manage'
  }
]