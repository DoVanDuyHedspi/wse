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
    }
  },
  getters: {
    getUsersDataTable: (state) => {
      return state.users.slice(0, 10)
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
    fetchOne({ commit }, id) {
      return new Promise((resolve, reject) => {
        axios.get(`/users/${id}/edit`)
          .then(response => {
            commit('FETCH_ONE', response.data);
            resolve(response);
          }, error => {
            reject(error)
          })
      })
    },
    fetchCompanyInfo({ commit }) {
      return axios.get('company/getInfo').then(response => {
        commit('FETCH_COMPANY_INFO', response.data)
      }).catch();
    },
    updateUsers({ commit }, users) {
      commit('UPDATE_USERS', users);
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