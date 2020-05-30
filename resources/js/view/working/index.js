import TimekeepingCalendarView from './TimekeepingCalendarView';
import TimekeepingTableView from './TimekeepingTableView';
import ManageTimeSheets from './ManageTimesheetsComponent';
import HistoryCheckInOut from './HistoryCheckInOutComponent';

export default [
  {
    path: '/timekeeping/calendar_view', component: TimekeepingCalendarView, name: 'working.timekeeping.calendar_view'
  },
  {
    path: '/timekeeping/table_view', component: TimekeepingTableView, name: 'working.timekeeping.table_view'
  },
  {
    path: '/manage_timesheets', component: ManageTimeSheets, name: 'working.manage', meta: {
      permission: 'update-timesheets',
    }
  }, 
  {
    path: '/history_checkinout', component: HistoryCheckInOut, name: 'working.history_checkinout', meta: {
      permission: 'update-timesheets', 
    }
  }
]