export default {
    state: {
        sites: [],
        site: null,
        all_sites: [],
        site_servers: [],
        deployment_steps: [],
        sites_listening_to: [],
        running_deployments: [],
        site_deployment_steps: []
    },
    actions: {
        getSite: ({ commit }, site) => {
            Vue.http.get(Vue.action('Site\SiteController@show', { site: site })).then((response) => {
                commit('SET_SITE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getSites: ({ commit, rootState }) => {
            if (rootState.userStore.user.current_pile_id != null) {
                return Vue.http.get(Vue.action('Pile\PileSitesController@index', { pile: rootState.userStore.user.current_pile_id })).then((response) => {
                    commit('SET_SITES', response.data)
                    return response.data
                }, (errors) => {
                    app.handleApiError(errors)
                })
            }
        },
        getAllSites: ({ commit, dispatch }) => {
            return Vue.http.get(Vue.action('Site\SiteController@index')).then((response) => {
                _.each(response.data, function (site) {
                    dispatch('listenToSite', site)
                })

                commit('SET_ALL_SITES', response.data)

                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        listenToSite: ({ commit, state }, site) => {
            if (_.indexOf(state.sites_listening_to, site.id) === -1) {
                commit('SET_SITES_LISTENING_TO', site)
                Echo.private('App.Models.Site.Site.' + site.id)
                    .listen('Site\\DeploymentStepStarted', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
                        commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
                    })
                    .listen('Site\\DeploymentStepCompleted', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
                    })
                    .listen('Site\\DeploymentStepFailed', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
                        commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
                    })
                    .listen('Site\\DeploymentCompleted', (data) => {
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
                        commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
                    })
                    .notification((notification) => {
                        if (notification.type === 'App\\Notifications\\Site\\NewSiteDeployment') {
                            commit('ADD_NEW_SITE_DEPLOYMENT', notification.siteDeployment)
                            commit('UPDATE_SITE_DEPLOYMENT_STATUS', notification.siteDeployment)
                        }
                    })
            }
        },
        createSite: ({ commit, dispatch, rootState }, data) => {
            return Vue.http.post(Vue.action('Site\SiteController@store'), {
                domain: data.domain,
                domainless: data.domainless,
                pile_id: rootState.userStore.user.current_pile_id
            }).then((response) => {
                commit('ADD_SITE', response.data)
                dispatch('listenToSite', response.data)
                app.$router.push({ name: 'site_repository', params: { site_id: response.data.id }})
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateSite: ({ commit }, data) => {
            Vue.http.put(Vue.action('Site\SiteController@update', { site: data.site_id }), data.data).then((response) => {
                commit('SET_SITE', response.data)
                app.showSuccess('You have updated the site')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteSite: ({ commit, rootState }, siteId) => {
            Vue.http.delete(Vue.action('Site\SiteController@destroy', { site: siteId })).then(() => {
                commit('DELETE_SITE', siteId)
                commit('REMOVE_SITE_FROM_PILE', {
                    site: siteId,
                    pile: rootState.userStore.user.current_pile_id
                })
                app.$router.push('/')
                app.showSuccess('You have deleted the site')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getSiteServers: ({ commit }, siteId) => {
            Vue.http.get(Vue.action('Site\SiteServerController@index', { site: siteId })).then((response) => {
                commit('SET_SITE_SERVERS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateLinkedServers: ({ commit, dispatch }, data) => {
            return Vue.http.post(Vue.action('Site\SiteServerController@store', { site: data.site }), data).then((response) => {
                dispatch('getSiteServers', data.site)
                return response.data
            })
        },
        getDeploymentSteps: ({ commit }, site) => {
            return Vue.http.get(Vue.action('Site\SiteDeploymentStepsController@getDeploymentSteps', { site: site })).then((response) => {
                commit('SET_DEPLOYMENT_STEPS', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getSiteDeploymentSteps: ({ commit }, site) => {
            return Vue.http.get(Vue.action('Site\SiteDeploymentStepsController@index', { site: site })).then((response) => {
                commit('SET_SITE_DEPLOYMENT_STEPS', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateSiteDeployment: ({}, data) => {
            Vue.http.post(Vue.action('Site\SiteDeploymentStepsController@store', { site: data.site }), data).then(() => {
                app.showSuccess('You have updated the site deployment')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartSiteWebServices: ({}, site) => {
            Vue.http.post(Vue.action('Site\SiteController@restartWebServices', { site: site })).then(() => {
                app.showSuccess('You have restarted your sites web services.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartSiteServers: ({}, site) => {
            Vue.http.post(Vue.action('Site\SiteController@restartServer', { site: site })).then(() => {
                app.showSuccess('You have restarted your sites servers.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartSiteDatabases: ({}, site) => {
            Vue.http.post(Vue.action('Site\SiteController@restartDatabases', { site: site })).then(() => {
                app.showSuccess('You have restarted your sites databases.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartSiteWorkers: ({}, site) => {
            Vue.http.post(Vue.action('Site\SiteController@restartWorkerServices', { site: site })).then(() => {
                app.showSuccess('You have restarted your sites workers.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createDeployHook: ({ commit }, site) => {
            Vue.http.post(Vue.action('Site\Repository\RepositoryHookController@store', { site: site })).then((response) => {
                commit('SET_SITE', response.data)
                app.showSuccess('You have added automatic deployments')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        removeDeployHook: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Site\Repository\RepositoryHookController@destroy', {
                site: data.site,
                hook: data.hook
            })).then((response) => {
                commit('SET_SITE', response.data)
                app.showSuccess('You have removed automatic deployments')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getRunningDeployments: ({ commit }) => {
            Vue.http.get(Vue.action('User\UserController@getRunningDeployments')).then((response) => {
                commit('SET_RUNNING_DEPLOYMENTS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deploySite: ({}, site) => {
            Vue.http.post(Vue.action('Site\SiteController@deploy', { site: site })).then((response) => {
                app.showSuccess('Your site deployment has been queued.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        rollbackSite: ({}, data) => {
            alert(data.siteDeployment)
            Vue.http.post(Vue.action('Site\SiteController@rollback', { site: data.site }), data).then((response) => {
                app.showSuccess('You are rolling back a deployment.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        refreshSshKeys: ({ commit }, site) => {
            Vue.http.post(Vue.action('Site\SiteController@refreshSshKeys', { site: site })).then((response) => {
                app.showSuccess('You refreshed your sites ssh keys.')
                commit('SET_SITE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        refreshDeployKey: ({ commit }, site) => {
            Vue.http.post(Vue.action('Site\SiteController@refreshDeployKey', { site: site })).then((response) => {
                app.showSuccess('You refreshed your sites deploy key.')
                commit('SET_SITE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        UNSET_SITE: (state) => {
            state.site = null
        },
        SET_SITE: (state, site) => {
            state.site = site
        },
        ADD_SITE: (state, site) => {
            state.sites.push(site)
        },
        DELETE_SITE: (state, site) => {
            Vue.set(state, 'sites', _.reject(state.sites, { id: site }))
            Vue.set(state, 'all_sites', _.reject(state.all_sites, { id: site }))
        },
        SET_SITES: (state, sites) => {
            state.sites = sites
        },
        SET_ALL_SITES: (state, sites) => {
            state.all_sites = sites
        },
        SET_SITE_SERVERS: (state, servers) => {
            state.site_servers = servers
        },
        REMOVE_SERVER_FROM_SITE_SERVERS: (state, server) => {
            Vue.set(state, 'site_servers', _.reject(state.site_servers, { id: server }))
        },
        SET_DEPLOYMENT_STEPS: (state, deploymentSteps) => {
            state.deployment_steps = deploymentSteps
        },
        SET_SITE_DEPLOYMENT_STEPS: (state, deploymentSteps) => {
            state.site_deployment_steps = deploymentSteps
        },
        SET_SITES_LISTENING_TO: (state, site) => {
            state.sites_listening_to.push(site.id)
        },
        UPDATE_SITE_SERVER: (state, server) => {
            const foundServer = _.find(state.site_servers, function (tempServer) {
                return tempServer.id === server.id
            })

            if (foundServer) {
                _.each(server, function (value, index) {
                    foundServer[index] = value
                })
            }
        },
        SET_SERVER_STATS: (state, data) => {
            const server = _.find(state.site_servers, { id: data.server })
            if (server) {
                Vue.set(server, 'stats', data.stats)
            }
        },
        SET_RUNNING_DEPLOYMENTS: (state, deployments) => {
            state.running_deployments = Object.keys(deployments).length > 0 ? deployments : {}
        },
        UPDATE_RUNNING_SITE_DEPLOYMENT: (state, event) => {
            if (!state.running_deployments[event.site_deployment.site_id]) {
                Vue.set(state.running_deployments, event.site_deployment.site_id, [])
            }

            const siteDeployments = state.running_deployments[event.site_deployment.site_id]
            const siteDeployment = siteDeployments[_.findKey(siteDeployments, { id: event.site_deployment.id })]

            if (siteDeployment) {
                _.each(event.site_deployment, function (value, key) {
                    if (key !== 'server_deployments') {
                        siteDeployment[key] = value
                    }
                })
            } else {
                siteDeployments.push(event.site_deployment)
            }
        },
        UPDATE_SITE_DEPLOYMENT_STATUS: (state, siteDeployment) => {

            const siteKey = _.findKey(state.sites, { id: siteDeployment.site_id })

            if (siteKey) {
                Vue.set(state.sites[siteKey], 'last_deployment_status', siteDeployment.status)
            }
        }
    }
}
