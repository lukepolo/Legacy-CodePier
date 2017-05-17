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
