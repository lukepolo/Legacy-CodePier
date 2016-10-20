export default {
    state: {
        sites: [],
        site: null,
        workers: [],
        site_servers: [],
        ssl_certificates: []
    },
    actions: {
        getSite: ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteController@show', {site: site})).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        getSites: ({commit}, callback) => {
            if (pileStore.state.current_pile_id) {
                Vue.http.get(Vue.action('Pile\PileSitesController@index', {pile: pileStore.state.current_pile_id})).then((response) => {
                    commit('SET_SITES', response.data);
                    typeof callback === 'function' && callback();
                }, (errors) => {
                    alert(error);
                });
            }
        },
        createSite: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@store'), {
                domain: data.domain,
                domainless: data.domainless,
                pile_id: pileStore.state.current_pile_id
            }).then((response) => {
                app.$router.push('/site/' + response.data.id);
            }, (errors) => {
                alert(error);
            })
        },
        updateSite: ({commit}, data) => {
            Vue.http.put(Vue.action('Site\SiteController@update', {site: data.site_id}), data.data).then((response) => {
                commit('SET_SITE', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        updateSiteServerFeatures: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteController@updateSiteServerFeatures', {site: data.site}), data.form).then((response) => {
                siteStore.dispatch('getSite', data.site);
            }, (errors) => {
                alert(error);
            });
        },
        deleteSite: ({commit}, site_id) => {
            Vue.http.delete(Vue.action('Site\SiteController@destroy', {site: site_id})).then((response) => {
                siteStore.dispatch('getSites');
                app.$router.push('/');
            }, (errors) => {

            })
        },
        getWorkers: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteWorkerController@show', {site: site_id})).then((response) => {
                commit('SET_WORKERS', response.data);
            }, (errors) => {
            });
        },
        installWorker: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteWorkerController@store', {site: data.site_id}), data).then((response) => {
                siteStore.dispatch('getWorkers', data.site_id);
            }, (errors) => {
                alert(error);
            });
        },
        deleteWorker: ({commit}, data) => {
            Vue.http.delete(Vue.action('Site\SiteWorkerController@destroy', {
                site: data.site,
                worker: data.worker
            })).then((response) => {
                siteStore.dispatch('getWorkers', data.site);
            }, (errors) => {
            });
        },
        getSslCertificates: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\Certificate\SiteSSLController@index', {site: site_id})).then((response) => {
                commit('SET_SSL_CERTIFICATES', response.data);
            }, (errors) => {
            });
        },
        installLetsEncryptSslCertificate: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\Certificate\SiteSSLLetsEncryptController@store', {site: data.site_id}), data).then((response) => {
                siteStore.dispatch('getSslCertificates', data.site_id);
            }, (errors) => {
            });
        },
        deleteSslCertificate: ({commit}, ssl_certificate_id) => {
            Vue.http.delete(Vue.action('Site\Certificate\SiteSSLController@destroy', {ssl: ssl_certificate_id})).then((response) => {
                siteStore.dispatch('getSslCertificates', siteStore.state.site.id);
            }, (errors) => {

            });
        },
        getSiteServers: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteServerController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_SERVERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        updateLinkedServers: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteServerController@store', {site: data.site}), data).then((response) => {
                siteStore.dispatch('getSiteServers', data.site);
            });
        },
        saveSiteFile: ({commit}, data) => {
            Vue.http.post(laroute.Vue.action('Site\SiteFileController@store', {
                site: data.site
            }), {
                file_path: data.file,
                content: data.content,
                servers: data.servers,
            }).then((response) => {

            }, (errors) => {
                alert(error);
            });
        },
        updateSiteFile: ({commit}, data) => {
            Vue.http.put(laroute.Vue.action('Site\SiteFileController@update', {
                site: data.site,
                file: data.file_id
            }), {
                file_path: data.file,
                content: data.content,
                servers: data.servers,
            }).then((response) => {

            }, (errors) => {
                alert(error);
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
        SET_WORKERS: (state, workers) => {
            state.workers = workers;
        },
        SET_SSL_CERTIFICATES: (state, ssl_certificates) => {
            state.ssl_certificates = ssl_certificates;
        },
        SET_SITE_SERVERS: (state, servers) => {
            state.site_servers = servers;
        }
    }
}