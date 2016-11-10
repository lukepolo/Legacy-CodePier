export default {
    state: {
        piles: [],
        user_piles: [],
    },
    actions: {
        getPiles: ({commit, dispatch}) => {
            Vue.http.get(Vue.action('Pile\PileController@index')).then((response) => {
                commit('SET_PILES', response.data);
            }, (errors) => {
                alert(errors);
            });
        },
        getUserPiles: ({commit}) => {
            Vue.http.get(Vue.action('Pile\PileController@index', {all: true})).then((response) => {
                commit('SET_USER_PILES', response.data);
            }, (errors) => {
                alert(errors);
            });
        },
        createPile: ({dispatch}, data) => {
            Vue.http.post(Vue.action('Pile\PileController@store'), data).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                app.showError(error);
            })
        },
        changePiles : ({commit, dispatch}, pileId) => {
            Vue.http.post(Vue.action('Pile\PileController@changePile'), {pile : pileId}).then((response) => {
                commit('SET_USER', response.data);
                dispatch('getServers');
                dispatch('getSites');

                app.$router.push('/');

            }, (errors) => {
                app.showError(error);
            })
        },
        updatePile: ({dispatch}, data) => {
            Vue.http.put(Vue.action('Pile\PileController@update', {pile: data.pile.id}), data).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                app.showError(error);
            })
        },
        deletePile: ({dispatch}, pile) => {
            Vue.http.delete(Vue.action('Pile\PileController@destroy', {pile: pile})).then((response) => {
                dispatch('getPiles');
            }, (errors) => {
                app.showError(error);
            })
        }
    },
    mutations: {
        SET_USER_PILES: (state, piles) => {
            state.user_piles = piles;
        },
        SET_PILES: (state, piles) => {
            state.piles = piles;
        }
    }
}