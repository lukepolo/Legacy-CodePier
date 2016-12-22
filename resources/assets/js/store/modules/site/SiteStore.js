export default {
    state: {
        sites: [],
        site: null,
        all_sites: [],
        site_files : [],
        site_servers: [],
        deployment_steps: [],
        sites_listening_to : [],
        running_deployments : [],
        site_deployment_steps: [],
    },
    actions: {
        getSite: ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteController@show', {site: site})).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        getSites: ({commit, rootState}) => {
            if (rootState.userStore.user.current_pile_id != null) {
                return Vue.http.get(Vue.action('Pile\PileSitesController@index', {pile: rootState.userStore.user.current_pile_id})).then((response) => {
                    commit('SET_SITES', response.data);
                    return response.data;
                }, (errors) => {
                    app.handleApiError(errors);
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
                app.handleApiError(errors);
            });
        },
        getSiteFiles : ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteFileController@index', {site: site})).then((response) => {
                commit('SET_SITE_FILES', response.data);
            }, (errors) => {
                app.handleApiError(errors);
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
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data);
                    })
                    .listen('Site\\DeploymentStepCompleted', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data);
                    })
                    .listen('Site\\DeploymentStepFailed', (data) => {
                        commit('UPDATE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data);
                    })
                    .listen('Site\\DeploymentCompleted', (data) => {
                        commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_SITE_DEPLOYMENT_EVENT', data);
                        commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data);
                    })
                    .notification((notification) => {
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
                dispatch('listenToSite', response.data);
                app.$router.push({ name : 'site_repository', params : { site_id : response.data.id}});
            }, (errors) => {
                app.handleApiError(errors);
            })
        },
        updateSite: ({commit}, data) => {
            Vue.http.put(Vue.action('Site\SiteController@update', {site: data.site_id}), data.data).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        deleteSite: ({commit}, site_id) => {
            Vue.http.delete(Vue.action('Site\SiteController@destroy', {site: site_id})).then(() => {
                commit('DELETE_SITE', site_id);
                app.$router.push('/');
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        getSiteServers: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteServerController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_SERVERS', response.data);
            }, (errors) => {
                app.handleApiError(errors);
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
                app.handleApiError(errors);
            });
        },
        getDeploymentSteps: ({commit}, site) => {
            return Vue.http.get(Vue.action('Site\SiteDeploymentStepsController@getDeploymentSteps', { site : site})).then((response) => {
                commit('SET_DEPLOYMENT_STEPS', response.data);
                return response.data;
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        getSiteDeploymentSteps: ({commit}, site) => {
            return Vue.http.get(Vue.action('Site\SiteDeploymentStepsController@index', { site : site})).then((response) => {
                commit('SET_SITE_DEPLOYMENT_STEPS', response.data);
                return response.data;
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        updateSiteDeployment: ({dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteDeploymentStepsController@store', { site : data.site}), data).then(() => {

            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        restartSiteWebServices: ({}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@restartWebServices', {site : data.site})).then(() => {
                app.showSuccess('You have restarted your sites web services.');
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        restartSiteServers: ({}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@restartServer', {site : data.site})).then(() => {
                app.showSuccess('You have restarted your sites servers.');
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        restartSiteDatabases: ({}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@restartDatabases', {site : data.site})).then(() => {
                app.showSuccess('You have restarted your sites databases.');
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        restartSiteWorkers: ({}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@restartWorkerServices', {site : data.site})).then(() => {
                app.showSuccess('You have restarted your sites workers.');
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        createDeployHook: ({commit}, site) => {
            Vue.http.post(Vue.action('Site\Repository\RepositoryHookController@store', {site : site})).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        removeDeployHook: ({commit}, data) => {
            Vue.http.delete(Vue.action('Site\Repository\RepositoryHookController@destroy', {
                site: data.site,
                hook: data.hook
            })).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        getRunningDeployments : ({commit}) => {
            Vue.http.get(Vue.action('User\UserController@getRunningDeployments')).then((response) => {
                commit('SET_RUNNING_DEPLOYMENTS', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        }
    },
    mutations: {
        SET_SITE: (state, site) => {
            state.site = site;
        },
        DELETE_SITE : (state, site_id) => {
            Vue.set(state, 'sites', _.reject(state.sites, { id : site_id}));
            Vue.set(state, 'all_sites', _.reject(state.all_sites, { id : site_id}));
        },
        SET_SITES: (state, sites) => {
            state.sites = sites;
        },
        SET_SITE_FILES: (state, files) => {
            state.site_files = files;
        },
        SET_ALL_SITES : (state, sites) => {
            state.all_sites = sites;
        },
        SET_SITE_SERVERS: (state, servers) => {
            state.site_servers = servers;
        },
        REMOVE_SERVER_FROM_SITE_SERVERS : (state, server_id) => {
            alert(server.id);
            Vue.set(state, 'site_servers', _.reject(state.site_servers, { id : server.id}));
        },
        SET_DEPLOYMENT_STEPS : (state, deployment_steps) => {
            state.deployment_steps = deployment_steps;
        },
        SET_SITE_DEPLOYMENT_STEPS : (state, deployment_steps) => {
            state.site_deployment_steps = deployment_steps;
        },
        SET_SITES_LISTENING_TO : (state, site) => {
            state.sites_listening_to.push(site.id);
        },
        UPDATE_SITE_SERVER: (state, server) => {
            let foundServer = _.find(state.site_servers, function(tempServer) {
                return tempServer.id == server.id
            });

            if(foundServer) {
                _.each(server, function(value, index) {
                    foundServer[index] = value;
                });
            }
        },
        SET_SERVER_STATS : (state, data) => {
            let server = _.find(state.site_servers, {id: data.server_id});
            if(server) {
                Vue.set(server, 'stats', data.stats);
            }
        },
        SET_RUNNING_DEPLOYMENTS : (state, deployments) => {
            state.running_deployments = Object.keys(deployments).length > 0 ? deployments : {};
        },
        UPDATE_RUNNING_SITE_DEPLOYMENT : (state, event) => {

            let siteDeployments = state.running_deployments[event.site_deployment.site_id];
            let siteDeployment = siteDeployments[_.findKey(siteDeployments, {id : event.site_deployment.id})];

            if(siteDeployment) {
                _.each(event.site_deployment, function(value, key) {
                    if(key != 'server_deployments') {
                        siteDeployment[key] = value;
                    }
                });
            } else {
                siteDeployments.push(event.site_deployment);
            }
        }
    }
}