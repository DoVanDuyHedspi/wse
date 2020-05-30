import TimekeepingCalendarView from './TimekeepingCalendarView';
import TimekeepingTableView from './TimekeepingTableView';
import ManageTimeSheets from './ManageTimesheetsComponent';
import HistoryCheckInOut from './HistoryCheckInOutComponent';
import OnHoliday from './OnHoliday';
import OnHolidayCreate from './OnHolidayCreate';
import OnLeaveType from './OnLeaveType';
import OnLeaveTypeCreate from './OnLeaveTypeCreate';
import OnHolidayEdit from './OnHolidayEdit'

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
  },
  {
    path: '/working-day/on-holiday', component: OnHoliday, name: 'working.onHoliday'
  },
  {
    path: '/working-day/on-holiday/create', component: OnHolidayCreate, name: 'working.onHoliday.create'
  },
  {
    path: '/working-day/on-holiday/edit/:id', component: OnHolidayEdit, name: 'working.onHoliday.edit'
  },
  {
    path: '/working-day/on-leave-type', component: OnLeaveType, name: 'working.onLeaveType'
  },
  {
    path: '/working-day/on-leave-type/create', component: OnLeaveTypeCreate, name: 'working.onLeaveType.create'
  },
]