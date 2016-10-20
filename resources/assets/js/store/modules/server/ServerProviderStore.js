export default {
    state: {
        user_server_providers: [],
        server_provider_options: [],
        server_provider_regions: [],
        server_provider_features: []
    },
    actions: {
        getUserServerProviders: ({commit, rootState}) => {
            Vue.http.get(Vue.action('User\Providers\UserServerProviderController@index', {user: rootState.userStore.user.id})).then((response) => {
                commit('SET_USER_SERVER_PROVIDERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        getServerProviderOptions: ({commit}, provider) => {
            Vue.http.get('/api/server/providers/' + provider + '/options').then((response) => {
                commit('SET_PROVIDER_SERVER_OPTIONS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        getServerProviderRegions: ({commit}, provider) => {
            Vue.http.get('/api/server/providers/' + provider + '/regions').then((response) => {
                commit('SET_PROVIDER_SERVER_REGIONS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        getServerProviderFeatures: ({commit}, provider) => {
            Vue.http.get('/api/server/providers/' + provider + '/features').then((response) => {
                commit('SET_PROVIDER_SERVER_FEATURES', response.data);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_USER_SERVER_PROVIDERS: (state, server_providers) => {
            state.user_server_providers = server_providers;
        },
        SET_PROVIDER_SERVER_OPTIONS: (state, server_provider_options) => {
            state.server_provider_options = server_provider_options;
        },
        SET_PROVIDER_SERVER_REGIONS: (state, server_provider_regions) => {
            state.server_provider_regions = server_provider_regions;
        },
        SET_PROVIDER_SERVER_FEATURES: (state, server_provider_features) => {
            state.server_provider_features = server_provider_features;
        }
    }
}