import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import { reject } from 'q';

Vue.use(Vuex);

const usersStore = new Vuex.Store({
  state: {
    users: [],
    user: {
      name: "",
      roles: [],
      permissions: [],
      email: "",
      phone_number: "",
      nationality: "",
      gender: "",
      birthday: "",
      official_start_day: "",
      personal_email: "",
      birthplace: "",
      current_address: "",
      permanent_address: "",
      tax_code: "",
      permissions: [],
      position_id: "",
      branch_id: "",
      employee_type_id: "",
      employee_code: "",
      group_id: [],
      vehicle: {
        type: "",
        brand: "",
        license_plates: ""
      },
      bank: {
        type: "",
        account_number: "",
        name: ""
      },
      education: {
        school: "",
        specialized: "",
        graduation_years: ""
      },
      identity_card_passport: {
        type: "",
        code: "",
        efective_date: "",
        expiry_date: "",
        issued_by: ""
      }
    },
    infoCompany: {
      roles: [],
      branches: [],
      groups: [],
      employee_type: [],
      permissions: [],
      positions: [],
    },
    events: [],
    users_timesheets: [],
    timekeeping: {
      morning_begin: "",
      morning_late: "",
      morning_end: "",
      afternoon_begin: "",
      afternoon_late: "",
      afternoon_end: ""
    }
  },
  mutations: {
    SET_USERS(state, users) {
      state.users = users;
    },
    SET_USER(state, user) {
      state.user = user;
    },
    SET_COMPANY_INFO(state, info) {
      state.infoCompany = info;
    },
    UPDATE_USERS(state, users) {
      state.users = users;
    },
    SET_EVENTS(state, events) {
      state.events = events;
    },
    SET_USERS_TIMESHEETS(state, users_timesheets) {
      state.users_timesheets = users_timesheets;
    },
    SET_TIMEKEEPING(state, timekeeping) {
      state.timekeeping = timekeeping;
    }
  },
  getters: {
    getUsersDataTable: (state) => {
      return state.users.slice(0, 10)
    },
    getListUserIds: (state) => (type) =>  {
      let list_id = [];
      if (type == 'work') {
        state.users_timesheets.map(function (user) {
          list_id.push(user.id);
        })
      } else if(type == 'users') {
        state.users.map(function (user) {
          list_id.push(user.id);
        })
      }
      return list_id;
    },
    getListSuggestions: (state) => {
      let listSuggestions = [];
      state.users.map(function (user) {
        listSuggestions.push({ "value": user.employee_code + ' ' + user.name });
      })
      return listSuggestions;
      // return [{"value":"vue"}, {"value":"duy"}];
    },
    getTimeSheetInfo: (state) => {
      let actual_penalty_time = 0;
      let number_of_fines = 0;
      let number_working_days = 0;
      let total_overtime = 0;
      state.events.map(function (event) {
        actual_penalty_time += event.fined_time;
        // if (event.number_of_fines == 1) {
        number_of_fines += event.number_of_fines;
        // }
        number_working_days += event.working_day;
        total_overtime = event.overtime;

      });
      let block_penalty_time = 0;
      if (actual_penalty_time % 30 == 0) {
        block_penalty_time = actual_penalty_time;
      } else {
        block_penalty_time = actual_penalty_time + 30 - actual_penalty_time % 30;
      }
      let data = [];
      data['actual_penalty_time'] = actual_penalty_time;
      data['block_penalty_time'] = block_penalty_time;
      data['number_of_fines'] = number_of_fines;
      data['number_working_days'] = number_working_days;
      data['total_overtime'] = total_overtime / 60;
      return data;
    },
    getDateOfMonth: (state) => {
      let dates = [];
      if (state.users_timesheets.length != 0) {
        state.users_timesheets[0].events.map(function (event) {
          dates.push(event.date);
        })
      }

      return dates;
    },
    getTimekeeping: (state) => {
      return state.timekeeping;
    },
    getTimeIn: (state) => (date) => {
      let timeIn_timeOut = '';
      state.events.map(function (event) {
        if (event.startDate == date) {
          timeIn_timeOut = event.title;
        }
      })
      let timeIn = timeIn_timeOut.split(" ")[0];
      return timeIn;
    },
    getTimeOut: (state) => (date) => {
      let timeIn_timeOut = '';
      state.events.map(function (event) {
        if (event.startDate == date) {
          timeIn_timeOut = event.title;
        }
      })
      let timeOut = timeIn_timeOut.split(" ")[2];
      return timeOut;
    }
  },
  actions: {
    fetch({ commit }) {
      return new Promise((resolve, reject) => {
        axios.get('api/users')
          .then(response => {
            commit('SET_USERS', response.data);
            resolve(response);
          }, error => {
            reject(error)
          })
      })
    },
    async fetchOne({ commit }, id) {
      let response = await axios.get(`api/users/${id}/edit`);
      if (response.data.status !== false) {
        commit('SET_USER', response.data);
      }
      return response;
    },
    fetchCompanyInfo({ commit }) {
      return axios.get('api/company/getInfo').then(response => {
        commit('SET_COMPANY_INFO', response.data)
      }).catch();
    },
    async fetchEvents({ commit }, params) {
      let response = await axios.get(`api/events/${params['id']}`, {
        params: {
          date: params['date']
        }
      });
      if (response.data.status !== false) {
        commit('SET_EVENTS', response.data.data);
      }
      return response;
    },
    async fetchUsersTimesheets({ commit }, filter) {
      let response = await axios.get(`api/events`, {
        params: filter
      });
      if (response.data.status !== false) {
        commit('SET_USERS_TIMESHEETS', response.data.data);
      }
      return response;
    },
    async fetchTimekeeping({ commit }) {
      let response = await axios.get(`api/company/getTimekeeping`);
      if (response.data.status !== false) {
        commit('SET_TIMEKEEPING', response.data);
      }
    },
    updateUsers({ commit }, users) {
      commit('UPDATE_USERS', users);
    },
    updateUsersTimesheets({ commit }, users_timesheets) {
      commit('SET_USERS_TIMESHEETS', users_timesheets);
    },
    deleteUser({ }, id) {
      return new Promise((resolve, reject) => {
        axios.delete(`api/users/${id}`)
          .then(response => {
            this.dispatch('fetch')
            resolve(response);
          }, error => {
            reject(error)
          })
      })
    }
  }
}
)

export default usersStore;