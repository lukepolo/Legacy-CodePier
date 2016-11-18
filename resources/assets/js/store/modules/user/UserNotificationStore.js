export default {
    state: {
        notification_providers: [],
        user_notification_providers : [],
    },
    actions: {
        getNotificationProviders: ({commit}) => {
            Vue.http.get(Vue.action('Auth\Providers\NotificationProvidersController@index')).then((response) => {
                commit('SET_NOTIFICATION_PROVIDERS', response.data);
            }, (errors) => {
                app.showError(error);
            });
        },
        getUserNotificationProviders: ({commit}, user_id) => {
            Vue.http.get(Vue.action('User\Providers\UserNotificationProviderController@index', {user: user_id})).then((response) => {
                commit('SET_USER_NOTIFICATION_PROVIDERS', response.data);
            }, (errors) => {
                app.showError(error);
            });
        },
        deleteUserNotificationProvider: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('User\Providers\UserNotificationProviderController@destroy', {
                user: data.user_id,
                notification_provider: data.user_notification_provider_id
            })).then((response) => {
                dispatch('getUserNotificationProviders');
            }, (errors) => {
                alert(errors);
            })
        },
    },
    mutations: {
        SET_NOTIFICATION_PROVIDERS: (state, providers) => {
            state.notification_providers = providers;
        },
        SET_USER_NOTIFICATION_PROVIDERS: (state, providers) => {
            state.user_notification_providers = providers;
        },
    }
}