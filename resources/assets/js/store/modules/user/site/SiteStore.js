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
            Vue.http.get().then((response) => {
                commit('SET_SITE_SERVERS', {
                    site: siteId,
                    servers: response.data
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
        SET_SITE: (state, site) => {
            state.site = site
        },
        SET_SITE_SERVERS: (state, { site, servers }) => {
            Vue.set(state.site_servers, site, servers)
        },
        SET_SERVER_STATS: (state, data) => {
            const server = _.find(state.site_servers, { id: data.server })
            if (server) {
                Vue.set(server, 'stats', data.stats)
            }
        }
    }
}
