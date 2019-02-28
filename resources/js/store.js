import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(VueAxios, axios);
Vue.use(Vuex);

export const store = new Vuex.Store({
  state: {
    passers: []
  },
  mutations: {
    setPassers(state, passers) {
      state.passers = passers;
    },
    clearPassers(state, passers) {
      state.passers = [];
    }
  },
  actions: {
    async fetchFromDatabase({ commit }) {
      const { data } = await Vue.axios.get('/api/get-passers');
      commit('setPassers', data.data);
    },

    async scrapeDataFromUrl({ commit }, url) {
      const response = await Vue.axios.get('/api/scrape');
      return response;
    },

    async addPassers({ commit }, data) {
      const { result } = await Vue.axios.post('/api/add-passers', data);
      return result;
    }
  },
  getters: {
    getPassers(state) {
      return state.passers;
    }
  }
});
