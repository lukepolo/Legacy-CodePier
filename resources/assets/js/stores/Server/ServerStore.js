import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

Vue.use(Vuex);

const serverStore = new Vuex.Store({
    state: {
        servers: [],
        server: null,
        server_sites: null,
        editable_server_files : [],
        editable_framework_files : [],
        available_server_features: [],
        available_server_languages: [],
        available_server_frameworks: []
    },
    actions: {
        getServer: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerController@show', {server: server_id})).then((response) => {
                commit('SET_SERVER', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getServers: ({commit}, callback) => {
            Vue.http.get(action('Server\ServerController@index', {pile_id: pileStore.state.current_pile_id})).then((response) => {
                commit('SET_SERVERS', response.json());
                typeof callback === 'function' && callback();
            }, (errors) => {
                alert('handle some error')
            });
        },
        createServer: ({commit}, data) => {
            $.ajax({
                type: "POST",
                url: action('Server\ServerController@store'),
                data: data,
                dataType: "json",
                success: function (data) {

                },
                error: function () {
                }
            });
        },
        archiveServer: ({commit}, server) => {
            Vue.http.delete(action('Server\ServerController@destroy', {server: server})).then((response) => {
                app.$router.push('/');
            }, (errors) => {
                alert(error);
            });
        },
        getServerSites: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerSiteController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_SITES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getServerAvailableFeatures: ({commit}) => {
            Vue.http.get(action('Server\ServerFeatureController@getServerFeatures')).then((response) => {
                commit('SET_AVAILABLE_SERVER_FEATURES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getServerAvailableLanguages: ({commit}) => {
            Vue.http.get(action('Server\ServerFeatureController@getLanguages')).then((response) => {
                commit('SET_AVAILABLE_SERVER_LANGUAGES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getServerAvailableFrameworks: ({commit}) => {
            Vue.http.get(action('Server\ServerFeatureController@getFrameworks')).then((response) => {
                commit('SET_AVAILABLE_SERVER_FRAMEWORKS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getEditableServerFiles: ({commit}, server) => {
            Vue.http.get(action('Server\ServerFeatureController@getEditableServerFiles', {server : server})).then((response) => {
                commit('SET_EDITABLE_SERVER_FILES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getEditableFrameworkFiles: ({commit}, site) => {
            Vue.http.get(action('Server\ServerFeatureController@getEditableFrameworkFiles', {site : site})).then((response) => {
                commit('SET_EDITABLE_FRAMEWORK_FILES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        installFeature: ({commit}, data) => {
            Vue.http.post(action('Server\ServerFeatureController@store', {server: data.server}), {
                service: data.service,
                feature: data.feature,
                parameters: data.parameters
            }).then((response) => {
                serverStore.dispatch('getServer', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        saveServerFile :({commit}, data) => {
            Vue.http.post(laroute.action('Server\ServerController@saveFile', {
                server: data.server
            }), {
                file: data.file,
                content: data.content,
            }).then((response) => {

            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER: (state, server) => {
            state.server = server;
        },
        SET_SERVERS: (state, servers) => {
            state.servers = servers;
        },
        SET_SERVER_SITES: (state, server_sites) => {
            state.server_sites = server_sites;
        },
        SET_AVAILABLE_SERVER_FEATURES: (state, available_server_features) => {
            state.available_server_features = available_server_features;
        },
        SET_AVAILABLE_SERVER_LANGUAGES: (state, available_server_languages) => {
            state.available_server_languages = available_server_languages;
        },
        SET_AVAILABLE_SERVER_FRAMEWORKS: (state, available_server_frameworks) => {
            state.available_server_frameworks = available_server_frameworks;
        },
        SET_EDITABLE_SERVER_FILES : (state, files) => {
            state.editable_server_files = files;
        },
        SET_EDITABLE_FRAMEWORK_FILES : (state, files) => {
            state.editable_framework_files = files;
        }
    }
});

export default serverStore