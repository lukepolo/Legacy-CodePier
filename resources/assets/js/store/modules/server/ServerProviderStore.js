export default {
    state: {
        server_providers : [],
        user_server_providers: [],
        server_provider_options: [],
        server_provider_regions: [],
        server_provider_features: []
    },
    actions: {
        getServerProviders : ({commit}) => {
            Vue.http.get(Vue.action('Auth\Providers\ServerProvidersController@index')).then((response) => {
                commit('SET_SERVER_PROVIDERS', response.data)
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServerProviderOptions: ({commit}, provider) => {
            Vue.http.get('/api/server/providers/' + provider + '/options').then((response) => {
                commit('SET_PROVIDER_SERVER_OPTIONS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServerProviderRegions: ({commit}, provider) => {
            Vue.http.get('/api/server/providers/' + provider + '/regions').then((response) => {
                commit('SET_PROVIDER_SERVER_REGIONS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServerProviderFeatures: ({commit}, provider) => {
            Vue.http.get('/api/server/providers/' + provider + '/features').then((response) => {
                commit('SET_PROVIDER_SERVER_FEATURES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        SET_SERVER_PROVIDERS : (state, providers) => {
            state.server_providers = providers;
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