import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const userStore = new Vuex.Store({
    state: {
        user: user,
        server_providers: [],
        repository_providers: [],
        notification_providers: []
    },
    actions: {
        getUserServerProviders: ({commit}) => {
            Vue.http.get(action('User\Providers\UserServerProviderController@index')).then((response) => {
                commit('SET_SERVER_PROVIDERS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserServerProvider: ({commit}, user_server_provider_id) => {
            Vue.http.delete(action('User\Providers\UserServerProviderController@destroy', { provider : user_server_provider_id })).then((response) => {
                userStore.dispatch('getUserServerProviders');
            }, (errors) => {
                alert('Trying to destory server');
            })
        },
        getUserRepositoryProviders: ({commit}) => {
            Vue.http.get(action('User\Providers\UserRepositoryProviderController@index')).then((response) => {
                commit('SET_REPOSITORY_PROVIDERS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserRepositoryProvider: ({commit}, user_repository_provider_id) => {
            Vue.http.delete(action('User\Providers\UserRepositoryProviderController@destroy', {provider: user_repository_provider_id})).then((response) => {
                userStore.dispatch('getUserRepositoryProviders');
            }, (errors) => {
                alert('Trying to delete user repository');
            })
        },
        getUserNotificationProviders: ({commit}) => {
            Vue.http.get(action('User\Providers\UserNotificationProviderController@index')).then((response) => {
                commit('SET_NOTIFICATION_PROVIDERS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        deleteUserNotificationProvider: ({commit}, user_notification_provider_id) => {
            Vue.http.delete(action('User\Providers\UserNotificationProviderController@destroy', { provider : user_notification_provider_id })).then((response) => {
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
});

export default userStore