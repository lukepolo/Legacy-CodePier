export default {
    state: {
        sites: [],
        site: null,
        site_servers: [],
        deployment_steps : [],
        site_deployment_steps : [],
    },
    actions: {
        getSite: ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteController@show', {site: site})).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.showError(error);
            });
        },
        getSites: ({commit, rootState}, callback) => {
            if (rootState.userStore.user.current_pile_id != null) {
                Vue.http.get(Vue.action('Pile\PileSitesController@index', {pile: rootState.userStore.user.current_pile_id})).then((response) => {
                    commit('SET_SITES', response.data);
                    typeof callback === 'function' && callback();
                }, (errors) => {
                    app.showError(error);
                });
            }
        },
        createSite: ({commit, dispatch, rootState}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@store'), {
                domain: data.domain,
                domainless: data.domainless,
                pile_id: rootState.userStore.user.current_pile_id
            }).then((response) => {
                app.$router.push('/site/' + response.data.id + '/repository');
                dispatch('getSites');
            }, (errors) => {
                app.showError(error);
            })
        },
        updateSite: ({commit}, data) => {
            Vue.http.put(Vue.action('Site\SiteController@update', {site: data.site_id}), data.data).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.showError(error);
            });
        },
        deleteSite: ({commit, dispatch}, site_id) => {
            Vue.http.delete(Vue.action('Site\SiteController@destroy', {site: site_id})).then((response) => {
                dispatch('getSites');
                app.$router.push('/');
            }, (errors) => {

            })
        },
        getSiteServers: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteServerController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_SERVERS', response.data);
            }, (errors) => {
                app.showError(error);
            });
        },
        updateLinkedServers: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteServerController@store', {site: data.site}), data).then((response) => {
                dispatch('getSiteServers', data.site);
            });
        },
        saveSiteFile: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteFileController@store', {
                site: data.site
            }), {
                file_path: data.file,
                content: data.content,
                servers: data.servers,
            }).then((response) => {

            }, (errors) => {
                app.showError(error);
            });
        },
        getDeploymentSteps: ({commit}, site) => {
            return Vue.http.get(Vue.action('Site\SiteDeploymentStepsController@getDeploymentSteps', { site : site})).then((response) => {
                commit('SET_DEPLOYMENT_STEPS', response.data);
                return response.data;
            });
        },
        getSiteDeploymentSteps: ({commit}, site) => {
            return Vue.http.get(Vue.action('Site\SiteDeploymentStepsController@index', { site : site})).then((response) => {
                commit('SET_SITE_DEPLOYMENT_STEPS', response.data);
                return response.data;
            });
        },
        updateSiteDeployment: ({dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteDeploymentStepsController@store', { site : data.site}), data).then((response) => {
                dispatch('getSiteDeploymentSteps', data.site);
            });
        }
    },
    mutations: {
        SET_SITE: (state, site) => {
            state.site = site;
        },
        SET_SITES: (state, sites) => {
            state.sites = sites;
        },
        SET_SITE_SERVERS: (state, servers) => {
            state.site_servers = servers;
        },
        SET_DEPLOYMENT_STEPS : (state, deployment_steps) => {
            state.deployment_steps = deployment_steps;
        },
        SET_SITE_DEPLOYMENT_STEPS : (state, deployment_steps) => {
            state.site_deployment_steps = deployment_steps;
        }
    }
}