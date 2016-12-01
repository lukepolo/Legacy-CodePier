export default {
    state: {
        sites: [],
        site: null,
        all_sites: [],
        site_servers: [],
        deployment_steps: [],
        sites_listening_to : [],
        site_deployment_steps: [],
    },
    actions: {
        getSite: ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteController@show', {site: site})).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        getSites: ({commit, rootState}) => {
            if (rootState.userStore.user.current_pile_id != null) {
                return Vue.http.get(Vue.action('Pile\PileSitesController@index', {pile: rootState.userStore.user.current_pile_id})).then((response) => {
                    commit('SET_SITES', response.data);
                    return response.data;
                }, (errors) => {
                    app.showError(errors);
                });
            }
        },
        getAllSites : ({commit, dispatch}) => {
            return Vue.http.get(Vue.action('Site\SiteController@index')).then((response) => {

                _.each(response.data, function(site) {
                    dispatch('listenToSite', site);
                });

                commit('SET_ALL_SITES', response.data);

                return response.data;
            }, (errors) => {
                app.showError(errors);
            });
        },
        listenToSite : ({commit, state}, site) => {
            if (_.indexOf(state.sites_listening_to, site.id) == -1) {
                commit('SET_SITES_LISTENING_TO', site);
                Echo.private('App.Models.Site.Site.' + site.id)
                    .listen('Site\\DeploymentStepStarted', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data);
                    })
                    .listen('Site\\DeploymentStepCompleted', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data);
                    })
                    .listen('Site\\DeploymentStepFailed', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data);
                    })
                    .listen('Site\\DeploymentCompleted', (data) => {
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data);
                    })
                    .notification((notification) => {
                        console.info(notification);
                        if(notification.type == 'App\\Notifications\\Site\\NewSiteDeployment') {
                            commit('ADD_NEW_SITE_DEPLOYMENT', notification.siteDeployment);
                        }
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

                dispatch('listenToSite', response.data);

                dispatch('getSites');
            }, (errors) => {
                app.showError(errors);
            })
        },
        updateSite: ({commit}, data) => {
            Vue.http.put(Vue.action('Site\SiteController@update', {site: data.site_id}), data.data).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.showError(errors);
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
                app.showError(errors);
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
                app.showError(errors);
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
        SET_ALL_SITES : (state, sites) => {
            state.all_sites = sites;
        },
        SET_SITE_SERVERS: (state, servers) => {
            state.site_servers = servers;
        },
        SET_DEPLOYMENT_STEPS : (state, deployment_steps) => {
            state.deployment_steps = deployment_steps;
        },
        SET_SITE_DEPLOYMENT_STEPS : (state, deployment_steps) => {
            state.site_deployment_steps = deployment_steps;
        },
        SET_SITES_LISTENING_TO : (state, site) => {
            state.sites_listening_to.push(site.id);
        }
    }
}