export default {
    state: {
        user: user,
        server_providers: [],
        repository_providers: [],
        notification_providers: []
    },
    actions: {
        getUserServerProviders: ({commit}, user_id) => {
            Vue.http.get(action('User\Providers\UserServerProviderController@index', { user : user_id })).then((response) => {
                commit('SET_SERVER_PROVIDERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserServerProvider: ({commit}, data) => {
            Vue.http.delete(action('User\Providers\UserServerProviderController@destroy', { user : data.user_id,  server_provider : data.user_server_provider_id })).then((response) => {
                userStore.dispatch('getUserServerProviders');
            }, (errors) => {
                alert('Trying to destory server');
            })
        },
        getUserRepositoryProviders: ({commit}, user_id) => {
            Vue.http.get(action('User\Providers\UserRepositoryProviderController@index', { user : user_id})).then((response) => {
                commit('SET_REPOSITORY_PROVIDERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserRepositoryProvider: ({commit}, data) => {
            Vue.http.delete(action('User\Providers\UserRepositoryProviderController@destroy', {user : data.user_id, repository_provider: data.user_repository_provider_id})).then((response) => {
                userStore.dispatch('getUserRepositoryProviders');
            }, (errors) => {
                alert('Trying to delete user repository');
            })
        },
        getUserNotificationProviders: ({commit}, user_id) => {
            Vue.http.get(action('User\Providers\UserNotificationProviderController@index', { user : user_id})).then((response) => {
                commit('SET_NOTIFICATION_PROVIDERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserNotificationProvider: ({commit}, data) => {
            Vue.http.delete(action('User\Providers\UserNotificationProviderController@destroy', { user : data.user_id, notification_provider : data.user_notification_provider_id })).then((response) => {
                userStore.dispatch('getUserNotificationProviders');
            }, (errors) => {
                alert('Trying to destroy notification');
            })
        }
    },
    mutations: {
        SET_SERVER_PROVIDERS: (state, providers) => {
            state.server_providers = providers;
        },
        SET_REPOSITORY_PROVIDERS: (state, providers) => {
            state.repository_providers = providers;
        },
        SET_NOTIFICATION_PROVIDERS: (state, providers) => {
            state.notification_providers = providers;
        }
    }
}