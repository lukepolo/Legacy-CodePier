export default {
  state: {
    servers: [],
    server: null,
    all_servers: [],
    server_sites: [],
    running_commands: {},
    servers_listening_to: [],
    editable_server_files: [],
    editable_framework_files: [],
    available_server_features: [],
    server_installed_features: [],
    available_server_languages: [],
    available_server_frameworks: [],
    servers_current_provisioning_step: {}
  },
  actions: {
    getServer: ({ commit }, server_id) => {
      Vue.http.get(Vue.action('Server\ServerController@show', { server: server_id })).then((response) => {
        commit('SET_SERVER', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getServersCurrentProvisioningStep: ({ commit }, server_id) => {
      Vue.http.get(Vue.action('Server\ServerProvisionStepsController@index', { server: server_id })).then((response) => {
        commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [server_id, response.data])
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    retryProvisioning: ({ commit }, server_id) => {
      Vue.http.post(Vue.action('Server\ServerProvisionStepsController@store', { server: server_id })).then((response) => {
        commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [server_id, response.data])
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getServers: ({ commit, rootState }) => {
      Vue.http.get(Vue.action('Server\ServerController@index', { pile_id: rootState.userStore.user.current_pile_id })).then((response) => {
        commit('SET_SERVERS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getAllServers: ({ commit, dispatch }) => {
      return Vue.http.get(Vue.action('Server\ServerController@index')).then((response) => {
        commit('SET_ALL_SERVERS', response.data)

        _.each(response.data, function (server) {
          dispatch('listenToServer', server)
        })
        return response.data
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    listenToServer: ({ commit, state, dispatch }, server) => {
      if (_.indexOf(state.servers_listening_to, server.id) == -1) {
        commit('SET_SERVERS_LISTENING_TO', server)

        if (server.progress < 100) {
          dispatch('getServersCurrentProvisioningStep', server.id)
        }

        Echo.private('App.Models.Server.Server.' + server.id)
                    .listen('Server\\ServerProvisionStatusChanged', (data) => {
                      commit('UPDATE_SERVER', data.server)
                      commit('UPDATE_SITE_SERVER', data.server)
                      commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [data.server.id, data.serverCurrentProvisioningStep])
                    })
                    .listen('Server\\ServerSshConnectionFailed', (data) => {
                      commit('UPDATE_SERVER', data.server)
                      commit('UPDATE_SITE_SERVER', data.server)
                    })
                    .listen('Server\\ServerSshConnectionFailed', (data) => {

                    })
                    .listen('Server\\ServerCommandUpdated', (data) => {
                      commit('UPDATE_COMMAND', data.command)
                      commit('UPDATE_EVENT_COMMAND', data.command)
                    })
                    .notification((notification) => {
                      switch (notification.type) {
                        case 'App\\Notifications\\Server\\ServerMemory':
                        case 'App\\Notifications\\Server\\ServerDiskUsage':
                        case 'App\\Notifications\\Server\\ServerLoad':

                          commit('SET_SERVER_STATS', {
                            server_id: server.id,
                            stats: notification.stats
                          })
                          break
                      }
                    })
      }
    },
    createServer: ({ dispatch, rootState }, form) => {
      return Vue.http.post(Vue.action('Server\ServerController@store'), form).then((response) => {
        rootState.serversStore.all_servers.push(response.data)
        dispatch('listenToServer', response.data)
        app.showSuccess('Your server is in queue to be provisioned')

        return response.data
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    archiveServer: ({ commit }, server) => {
      Vue.http.delete(Vue.action('Server\ServerController@destroy', { server: server })).then(() => {
        if (app.$router.currentRoute.params.server_id) {
          app.$router.push('/')
        }

        commit('REMOVE_SERVER', server)
        commit('REMOVE_SERVER_FROM_SITE_SERVERS', server)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getServerSites: ({ commit }, server_id) => {
      Vue.http.get(Vue.action('Server\ServerSiteController@index', { server: server_id })).then((response) => {
        commit('SET_SERVER_SITES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getServerAvailableFeatures: ({ commit }) => {
      Vue.http.get(Vue.action('Server\ServerFeatureController@getFeatures')).then((response) => {
        commit('SET_AVAILABLE_SERVER_FEATURES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getServerAvailableLanguages: ({ commit }) => {
      Vue.http.get(Vue.action('Server\ServerFeatureController@getLanguages')).then((response) => {
        commit('SET_AVAILABLE_SERVER_LANGUAGES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getServerAvailableFrameworks: ({ commit }) => {
      Vue.http.get(Vue.action('Server\ServerFeatureController@getFrameworks')).then((response) => {
        commit('SET_AVAILABLE_SERVER_FRAMEWORKS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getEditableServerFiles: ({ commit }, server) => {
      Vue.http.get(Vue.action('Server\ServerFeatureController@getEditableFiles', { server: server })).then((response) => {
        commit('SET_EDITABLE_SERVER_FILES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getEditableFrameworkFiles: ({ commit }, site) => {
      Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFrameworkFiles', { site: site })).then((response) => {
        commit('SET_EDITABLE_FRAMEWORK_FILES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    installFeature: ({}, data) => {
      Vue.http.post(Vue.action('Server\ServerFeatureController@store', { server: data.server }), {
        service: data.service,
        feature: data.feature,
        parameters: data.parameters
      }).then((response) => {
        alert('install server feature')
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    saveServerFile: ({}, data) => {
      Vue.http.post(Vue.action('Server\ServerController@saveFile', {
        server: data.server
      }), {
        file: data.file,
        content: data.content
      }).then((response) => {

      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getRunningCommands: ({ commit }) => {
      Vue.http.get(Vue.action('User\UserController@getRunningCommands')).then((response) => {
        commit('SET_RUNNING_COMMANDS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getServerFeatures: ({ commit }, server) => {
      Vue.http.get(Vue.action('Server\ServerFeatureController@index', { server: server })).then((response) => {
        commit('SET_SERVER_INSTALLED_FEATURES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    SET_SERVER: (state, server) => {
      state.server = server
    },
    SET_SERVERS: (state, servers) => {
      state.servers = servers
    },
    SET_SERVER_SITES: (state, server_sites) => {
      state.server_sites = server_sites
    },
    SET_AVAILABLE_SERVER_FEATURES: (state, available_server_features) => {
      state.available_server_features = available_server_features
    },
    SET_AVAILABLE_SERVER_LANGUAGES: (state, available_server_languages) => {
      state.available_server_languages = available_server_languages
    },
    SET_AVAILABLE_SERVER_FRAMEWORKS: (state, available_server_frameworks) => {
      state.available_server_frameworks = available_server_frameworks
    },
    SET_EDITABLE_SERVER_FILES: (state, files) => {
      state.editable_server_files = files
    },
    SET_EDITABLE_FRAMEWORK_FILES: (state, files) => {
      state.editable_framework_files = files
    },
    SET_SERVERS_CURRENT_PROVISIONING_STEP: (state, [server_id, current_step]) => {
      const servers_current_provisioning_steps = {}

      servers_current_provisioning_steps[server_id] = current_step

      _.each(state.servers_current_provisioning_steps, function (current_step, server_id) {
        servers_current_provisioning_steps[server_id] = current_step
      })

      state.servers_current_provisioning_step = servers_current_provisioning_steps
    },
    UPDATE_SERVER: (state, server) => {
      const foundServer = _.find(state.servers, function (tempServer) {
        return tempServer.id == server.id
      })

      if (foundServer) {
        _.each(server, function (value, index) {
          foundServer[index] = value
        })
      }
    },
    SET_ALL_SERVERS: (state, servers) => {
      state.all_servers = servers
    },
    SET_SERVERS_LISTENING_TO: (state, server) => {
      state.servers_listening_to.push(server.id)
    },
    SET_RUNNING_COMMANDS: (state, commands) => {
      state.running_commands = Object.keys(commands).length > 0 ? commands : {}
    },
    UPDATE_COMMAND: (state, command) => {
      const commandKey = _.findKey(state.running_commands[command.commandable_type], { id: command.id })

      if (commandKey) {
        return Vue.set(state.running_commands[command.commandable_type], commandKey, command)
      }

      if (!state.running_commands[command.commandable_type] || !_.isArray(state.running_commands[command.commandable_type])) {
        Vue.set(state.running_commands, command.commandable_type, [])
      }

      state.running_commands[command.commandable_type].push(command)
    },
    REMOVE_SERVER: (state, server) => {
      Vue.set(state, 'servers', _.reject(state.servers, { id: server }))
      Vue.set(state, 'all_servers', _.reject(state.all_servers, { id: server }))
    },
    SET_SERVER_INSTALLED_FEATURES: (state, server_features) => {
      state.server_installed_features = server_features
    }
  }
}
