export default {
    state: {
        sites: [],
        site: null,
        all_sites: [],
        site_servers: {},
        deployment_steps: [],
        sites_listening_to: [],
        running_deployments: [],
        site_deployment_steps: []
    },
    actions: {

        getSiteServers: ({ commit }, siteId) => {
            Vue.http.get(Vue.action('Site\SiteServerController@index', { site: siteId })).then((response) => {
                commit('SET_SITE_SERVERS', {
                    site : siteId,
                    servers : response.data
                })
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
        getDeployment : ({commit}, data) => {
            return Vue.http.get(Vue.action('Site\SiteDeploymentsController@show', { site :  data.site, deployment: data.deployment })).then((response) => {
                commit('ADD_NEW_SITE_DEPLOYMENT', response.data)
                commit('UPDATE_SITE_DEPLOYMENT_STATUS', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
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

        deploySite: ({}, site) => {
            Vue.http.post(Vue.action('Site\SiteController@deploy', { site: site })).then((response) => {
                app.showSuccess('Your site deployment has been queued.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        rollbackSite: ({}, data) => {
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
        SET_SITE_SERVERS: (state, {site, servers}) => {
            Vue.set(state.site_servers, site, servers)
        },
        REMOVE_SERVER_FROM_SITE_SERVERS: (state, server) => {
            _.each(state.site_servers, (servers, site) => {
                Vue.set(state.site_servers, site, _.reject(state.site_servers[site], { id: server }))
            })
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
            _.each(state.site_servers, (servers) => {
                let foundServer = _.find(servers, (tempServer) => {
                    return tempServer.id === server.id
                })

                if (foundServer) {
                    _.each(server, function (value, index) {

                        foundServer[index] = value
                    })
                }
            })
        },
        SET_SERVER_STATS: (state, data) => {
            const server = _.find(state.site_servers, { id: data.server })
            if (server) {
                Vue.set(server, 'stats', data.stats)
            }
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
