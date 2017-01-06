export default {
  state: {
    piles: [],
    all_user_piles: []
  },
  actions: {
    getPiles: ({ commit }) => {
      Vue.http.get(Vue.action('Pile\PileController@index')).then((response) => {
        commit('SET_PILES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getAllUserPiles: ({ commit }) => {
      Vue.http.get(Vue.action('Pile\PileController@allPiles')).then((response) => {
        commit('SET_ALL_USER_PILES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getPileSites: ({ commit }, pileId) => {
      Vue.http.get(Vue.action('Pile\PileSitesController@index', { pile: pileId })).then((response) => {
        commit('SET_PILE_SITES', {
          pile: pileId,
          sites: response.data
        })
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    createPile: ({ commit }, data) => {
      Vue.http.post(Vue.action('Pile\PileController@store'), data).then((response) => {
        commit('ADD_PILE', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    changePiles: ({ commit, dispatch }, pileId) => {
      Vue.http.post(Vue.action('Pile\PileController@changePile'), { pile: pileId }).then((response) => {
        commit('SET_USER', response.data)
        dispatch('getServers')
        dispatch('getSites')

        if (app.$router.currentRoute.params.server_id || app.$router.currentRoute.params.site_id) {
          app.$router.push('/')
        }
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    updatePile: ({}, data) => {
      Vue.http.put(Vue.action('Pile\PileController@update', { pile: data.pile.id }), data).then((response) => {

      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    deletePile: ({ commit }, pile) => {
      Vue.http.delete(Vue.action('Pile\PileController@destroy', { pile: pile })).then((response) => {
        commit('REMOVE_PILE', pile)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    ADD_PILE: (state, pile) => {
      state.piles.push(pile)
    },
    REMOVE_PILE: (state, pile_id) => {
      Vue.set(state, 'piles', _.reject(state.piles, { id: pile_id }))
      Vue.set(state, 'all_user_piles', _.reject(state.all_user_piles, { id: pile_id }))
    },
    SET_ALL_USER_PILES: (state, piles) => {
      state.all_user_piles = piles
    },
    SET_PILES: (state, piles) => {
      state.piles = piles
    },
    SET_PILE_SITES: (state, data) => {
      const pile = _.find(state.piles, { id: data.pile })
      Vue.set(pile, 'sites', data.sites)
    },
    REMOVE_SITE_FROM_PILE: (state, data) => {
      const pile = _.find(state.piles, { id: data.pile })
      Vue.set(pile, 'sites', _.reject(pile, data.site))
    }
  }
}
