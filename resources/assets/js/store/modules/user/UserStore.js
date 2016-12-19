export default {
    state: {
        user: user,

        server_providers : [],
        user_server_providers: [],

        repository_providers: [],
        user_repository_providers: [],
    },
    actions: {
        getCurrentUser : ({commit}) => {
            Vue.http.get(Vue.action('User\UserController@index')).then((response) => {
                commit('SET_USER', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        updateUser: ({commit}, form) => {
            Vue.http.put(Vue.action('User\UserController@update', {user: form.user_id}), form, {}).then((response) => {
                commit('SET_USER', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },


        getUserServerProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserServerProviderController@index', {user: user_id})).then((response) => {
                commit('SET_USER_SERVER_PROVIDERS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteUserServerProvider: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserServerProviderController@destroy', {
                user: data.user_id,
                server_provider: data.user_server_provider_id
            })).then((response) => {
                dispatch('getUserServerProviders');
            }, (errors) => {
                app.showError(errors);
            })
        },

        getRepositoryProviders: ({commit}) => {
            Vue.http.get(Vue.action('Auth\Providers\RepositoryProvidersController@index')).then((response) => {
                commit('SET_REPOSITORY_PROVIDERS', response.data)
            }, (errors) => {
                app.showError(error);
            });
        },
        getUserRepositoryProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserRepositoryProviderController@index', {user: user_id})).then((response) => {
                commit('SET_USER_REPOSITORY_PROVIDERS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteUserRepositoryProvider: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserRepositoryProviderController@destroy', {
                user: data.user_id,
                repository_provider: data.user_repository_provider_id
            })).then((response) => {
                dispatch('getUserRepositoryProviders');
            }, (errors) => {
                app.showError(errors);
            })
        },

    },
    mutations: {
        SET_USER: (state, user) => {
            state.user = user;
        },

        SET_USER_SERVER_PROVIDERS: (state, providers) => {
            state.user_server_providers = providers;
        },

        SET_REPOSITORY_PROVIDERS : (state, providers) => {
            state.repository_providers = providers;
        },
        SET_USER_REPOSITORY_PROVIDERS: (state, providers) => {
            state.user_repository_providers = providers;
        },
    }
}