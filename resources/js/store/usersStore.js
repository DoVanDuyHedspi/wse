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
  },
  mutations: {
    FETCH(state, users) {
      state.users = users;
    },
    FETCH_ONE(state, user) {
      state.user = user;
    },
    FETCH_COMPANY_INFO(state, info) {
      state.infoCompany = info;
    },
    UPDATE_USERS(state, users) {
      state.users = users;
    },
    FETCH_EVENTS(state, events) {
      state.events = events;
    },
    FETCH_USERS_TIMESHEETS(state, users_timesheets) {
      state.users_timesheets = users_timesheets;
    }
  },
  getters: {
    getUsersDataTable: (state) => {
      return state.users.slice(0, 10)
    },
    getInfoPenalty: (state) => {
      let actual_penalty_time = 0;
      let number_of_fines = 0;
      let number_working_days = 0;
      state.events.map(function (event) {
        actual_penalty_time = actual_penalty_time + event.fined_time;
        if (event.number_of_fines == 1) {
          number_of_fines++;
        }
        number_working_days = number_working_days + event.working_day;
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
    }
  },
  actions: {
    fetch({ commit }) {
      return new Promise((resolve, reject) => {
        axios.get('/users')
          .then(response => {
            commit('FETCH', response.data);
            resolve(response);
          }, error => {
            reject(error)
          })
      })
    },
    async fetchOne({ commit }, id) {
      let response = await axios.get(`/users/${id}/edit`);
      if (response.data.status !== false) {
        commit('FETCH_ONE', response.data);
      }
      return response;
    },
    fetchCompanyInfo({ commit }) {
      return axios.get('company/getInfo').then(response => {
        commit('FETCH_COMPANY_INFO', response.data)
      }).catch();
    },
    async fetchEvents({ commit }, params) {
      let response = await axios.get(`api/events/${params['id']}`, {
        params: {
          date: params['date']
        }
      });
      if (response.data.status !== false) {
        commit('FETCH_EVENTS', response.data.data);
      }
      return response;
    },
    async fetchUsersTimesheets({ commit }) {
      let response = await axios.get(`api/events`);
      if (response.data.status !== false) {
        commit('FETCH_USERS_TIMESHEETS', response.data.data);
      }
      return response;
    },
    updateUsers({ commit }, users) {
      commit('UPDATE_USERS', users);
    },
    updateUsersTimesheets({ commit }, users_timesheets) {
      commit('FETCH_USERS_TIMESHEETS', users_timesheets);
    },
    deleteUser({ }, id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/users/${id}`)
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