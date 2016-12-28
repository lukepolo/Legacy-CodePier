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
                app.handleApiError(errors);
            });
        },
        updateUser: ({commit}, form) => {
            Vue.http.put(Vue.action('User\UserController@update', {user: form.user_id}), form, {}).then((response) => {
                commit('SET_USER', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },

        getUserServerProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserServerProviderController@index', {user: user_id})).then((response) => {
                commit('SET_USER_SERVER_PROVIDERS', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        deleteUserServerProvider: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserServerProviderController@destroy', {
                user: data.user_id,
                server_provider: data.user_server_provider_id
            })).then((response) => {
                commit('REMOVE_SERVER_PROVIDER_FROM_USER', data.user_server_provider_id)
            }, (errors) => {
                app.handleApiError(errors);
            })
        },

        getRepositoryProviders: ({commit}) => {
            Vue.http.get(Vue.action('Auth\Providers\RepositoryProvidersController@index')).then((response) => {
                commit('SET_REPOSITORY_PROVIDERS', response.data)
            }, (errors) => {
                app.handleApiError(error);
            });
        },
        getUserRepositoryProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserRepositoryProviderController@index', {user: user_id})).then((response) => {
                commit('SET_USER_REPOSITORY_PROVIDERS', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        deleteUserRepositoryProvider: ({dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserRepositoryProviderController@destroy', {
                user: data.user_id,
                repository_provider: data.user_repository_provider_id
            })).then((response) => {
                commit('REMOVE_REPOSITORY_PROVIDER_FROM_USER', data.user_repository_provider_id)
            }, (errors) => {
                app.handleApiError(errors);
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
        REMOVE_SERVER_PROVIDER_FROM_USER: (state, provider_id) =>{
            Vue.set(state, 'user_server_providers', _.reject(state.user_server_providers, { id : provider_id}));
        },

        SET_REPOSITORY_PROVIDERS : (state, providers) => {
            state.repository_providers = providers;
        },
        SET_USER_REPOSITORY_PROVIDERS: (state, providers) => {
            state.user_repository_providers = providers;
        },
        REMOVE_REPOSITORY_PROVIDER_FROM_USER: (state, provider_id) =>{
            Vue.set(state, 'user_repository_providers', _.reject(state.user_repository_providers, { id : provider_id}));
        }
    }
}