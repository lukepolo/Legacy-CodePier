export default {
    state: {
        servers: [],
        server: null,
        all_servers : [],
        server_sites: [],
        servers_listening_to : [],
        editable_server_files: [],
        editable_framework_files: [],
        available_server_features: [],
        available_server_languages: [],
        available_server_frameworks: [],
        servers_current_provisioning_step : {},
        runningCommands : Object.keys(runningCommands).length > 0 ? runningCommands : {},
    },
    actions: {
        getServer: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerController@show', {server: server_id})).then((response) => {
                commit('SET_SERVER', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServersCurrentProvisioningStep: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerProvisionStepsController@index', {server: server_id})).then((response) => {
                commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [server_id, response.data]);
            }, (errors) => {
                app.showError(errors);
            });
        },
        retryProvisioning:  ({commit}, server_id) => {
            Vue.http.post(Vue.action('Server\ServerProvisionStepsController@store', {server: server_id})).then((response) => {
                commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [server_id, response.data]);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServers: ({commit, rootState}) => {
            Vue.http.get(Vue.action('Server\ServerController@index', {pile_id: rootState.userStore.user.current_pile_id})).then((response) => {
                commit('SET_SERVERS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getAllServers: ({commit, dispatch}) => {
            return Vue.http.get(Vue.action('Server\ServerController@index')).then((response) => {
                commit('SET_ALL_SERVERS', response.data);

                _.each(response.data, function(server) {
                    dispatch('listenToServer', server)
                });
                return response.data;
            }, (errors) => {
                app.showError(errors);
            });
        },
        listenToServer : ({commit, state, dispatch}, server) => {
            if (_.indexOf(state.servers_listening_to, server.id) == -1) {

                commit('SET_SERVERS_LISTENING_TO', server);

                if(server.progress < 100) {
                    dispatch('getServersCurrentProvisioningStep', server.id)
                }

                Echo.private('App.Models.Server.Server.' + server.id)
                    .listen('Server\\ServerProvisionStatusChanged', (data) => {
                        commit("UPDATE_SERVER", data.server);
                        commit("UPDATE_SITE_SERVER", data.server);
                        commit("SET_SERVERS_CURRENT_PROVISIONING_STEP", [data.server.id, data.serverCurrentProvisioningStep]);
                    })
                    .listen('Server\\ServerSshConnectionFailed', (data) => {
                        commit("UPDATE_SERVER", data.server);
                        commit("UPDATE_SITE_SERVER", data.server);
                    })
                    .listen('Server\\ServerSshConnectionFailed', (data) => {

                    })
                    .listen('Server\\ServerCommandUpdated', (data) => {
                        commit("UPDATE_COMMAND", data.command);
                    })
                    .notification((notification) => {
                        switch(notification.type) {
                            case 'App\\Notifications\\Server\\ServerMemory':
                            case 'App\\Notifications\\Server\\ServerDiskUsage':
                            case 'App\\Notifications\\Server\\ServerLoad':

                                commit('SET_SERVER_STATS', {
                                    server_id : server.id,
                                    stats : notification.stats
                                });
                                break;
                        }
                    })
            }
        },
        createServer: ({dispatch, rootState}, form) => {
            Vue.http.post(Vue.action('Server\ServerController@store'), form).then((response) => {
                rootState.serversStore.all_servers.push(response.data);
                dispatch('listenToServer', response.data);
                app.showSuccess('Your server is in queue to be provisioned');
            }, (errors) => {
                app.showError(errors);
            });
        },
        archiveServer: ({commit}, server) => {
            Vue.http.delete(Vue.action('Server\ServerController@destroy', {server: server})).then(() => {
                app.$router.push('/');
                commit('REMOVE_SERVER', server);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServerSites: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerSiteController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_SITES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServerAvailableFeatures: ({commit}) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getFeatures')).then((response) => {
                commit('SET_AVAILABLE_SERVER_FEATURES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServerAvailableLanguages: ({commit}) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getLanguages')).then((response) => {
                commit('SET_AVAILABLE_SERVER_LANGUAGES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getServerAvailableFrameworks: ({commit}) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getFrameworks')).then((response) => {
                commit('SET_AVAILABLE_SERVER_FRAMEWORKS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getEditableServerFiles: ({commit}, server) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getEditableFiles', {server: server})).then((response) => {
                commit('SET_EDITABLE_SERVER_FILES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getEditableFrameworkFiles: ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFrameworkFiles', {site: site})).then((response) => {
                commit('SET_EDITABLE_FRAMEWORK_FILES', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        installFeature: ({}, data) => {
            Vue.http.post(Vue.action('Server\ServerFeatureController@store', {server: data.server}), {
                service: data.service,
                feature: data.feature,
                parameters: data.parameters
            }).then((response) => {
                alert('install server feature');
            }, (errors) => {
                app.showError(errors);
            });
        },
        saveServerFile: ({}, data) => {
            Vue.http.post(Vue.action('Server\ServerController@saveFile', {
                server: data.server
            }), {
                file: data.file,
                content: data.content,
            }).then((response) => {

            }, (errors) => {
                app.showError(errors);
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
        SET_EDITABLE_SERVER_FILES: (state, files) => {
            state.editable_server_files = files;
        },
        SET_EDITABLE_FRAMEWORK_FILES: (state, files) => {
            state.editable_framework_files = files;
        },
        SET_SERVERS_CURRENT_PROVISIONING_STEP: (state, [server_id, current_step]) => {

            let servers_current_provisioning_steps = {};

            servers_current_provisioning_steps[server_id] = current_step;

            _.each(state.servers_current_provisioning_steps, function(current_step, server_id) {
                servers_current_provisioning_steps[server_id] = current_step;
            });

            state.servers_current_provisioning_step = servers_current_provisioning_steps;
        },
        UPDATE_SERVER : (state, server) => {
            let foundServer = _.find(state.servers, function(tempServer) {
               return tempServer.id == server.id
            });

            if(foundServer) {
                _.each(server, function(value, index) {
                    foundServer[index] = value;
                });
            }

        },
        SET_ALL_SERVERS : (state, servers) => {
            state.all_servers = servers;
        },
        SET_SERVERS_LISTENING_TO : (state, server) => {
            state.servers_listening_to.push(server.id);
        },
        UPDATE_COMMAND : (state, command) => {

            let commandKey = _.findKey(state.runningCommands[command.commandable_type], { id: command.id });

            if(commandKey) {
                return Vue.set(state.runningCommands[command.commandable_type], commandKey, command);
            }

            if(!state.runningCommands[command.commandable_type]) {
                Vue.set(state.runningCommands, command.commandable_type, []);
            }


            state.runningCommands[command.commandable_type].push(command);
        },
        REMOVE_SERVER : (state, server) => {
            Vue.set(rootState.serversStore, 'servers', _.reject(rootState.serversStore.servers, { id : server}));
            Vue.set(rootState.serversStore, 'all_servers', _.reject(rootState.serversStore.all_servers, { id : server}));
        }
    }
}