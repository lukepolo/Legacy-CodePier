export default {
    state: {
        user: user,
        server_providers: [],
        repository_providers: [],
        notification_providers: [],
    },
    actions: {
        getCurrentUser : ({commit}) => {
            Vue.http.get(Vue.action('User\UserController@index')).then((response) => {
                commit('SET_USER', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        getUserServerProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserServerProviderController@index', {user: user_id})).then((response) => {
                commit('SET_SERVER_PROVIDERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        updateUser: ({commit}, form) => {
            Vue.http.put(Vue.action('User\UserController@update', {user: form.user_id}), form, {}).then((response) => {
                commit('SET_USER', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserServerProvider: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserServerProviderController@destroy', {
                user: data.user_id,
                server_provider: data.user_server_provider_id
            })).then((response) => {
                dispatch('getUserServerProviders');
            }, (errors) => {
                alert('Trying to destory server');
            })
        },
        getUserRepositoryProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserRepositoryProviderController@index', {user: user_id})).then((response) => {
                commit('SET_REPOSITORY_PROVIDERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserRepositoryProvider: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserRepositoryProviderController@destroy', {
                user: data.user_id,
                repository_provider: data.user_repository_provider_id
            })).then((response) => {
                dispatch('getUserRepositoryProviders');
            }, (errors) => {
                alert('Trying to delete user repository');
            })
        },
        getUserNotificationProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserNotificationProviderController@index', {user: user_id})).then((response) => {
                commit('SET_NOTIFICATION_PROVIDERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserNotificationProvider: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserNotificationProviderController@destroy', {
                user: data.user_id,
                notification_provider: data.user_notification_provider_id
            })).then((response) => {
                dispatch('getUserNotificationProviders');
            }, (errors) => {
                alert('Trying to destroy notification');
            })
        },
        getPlans: ({commit}) => {
            Vue.http.get(Vue.action('SubscriptionController@index')).then((response) => {
                commit('SET_PLANS', response.data);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_USER: (state, user) => {
            state.user = user;
        },
        SET_SERVER_PROVIDERS: (state, providers) => {
            state.server_providers = providers;
        },
        SET_REPOSITORY_PROVIDERS: (state, providers) => {
            state.repository_providers = providers;
        },
        SET_NOTIFICATION_PROVIDERS: (state, providers) => {
            state.notification_providers = providers;
        },
        SET_PLANS: (state, plans) => {
            state.plans = plans;
        }
    }
}