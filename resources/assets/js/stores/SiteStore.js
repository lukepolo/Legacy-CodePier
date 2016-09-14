import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const siteStore = new Vuex.Store({
    state: {
        sites: [],
        site: null,
        workers: [],
        site_servers: [],
        ssl_certificates: []
    },
    actions: {
        getSite: ({commit}, site) => {
            Vue.http.get(action('Site\SiteController@show', {site: site})).then((response) => {
                commit('SET_SITE', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getSites: ({commit}, callback) => {
            if (pileStore.state.current_pile_id) {
                Vue.http.get(action('Pile\PileSitesController@index', {pile: pileStore.state.current_pile_id})).then((response) => {
                    commit('SET_SITES', response.json());
                    typeof callback === 'function' && callback();
                }, (errors) => {
                    alert(error);
                });
            }
        },
        createSite: ({commit}, data) => {
            Vue.http.post(action('Site\SiteController@store'), {
                domain: data.domain,
                domainless: data.domainless,
                pile_id: pileStore.state.current_pile_id
            }).then((response) => {
                app.$router.push('/site/' + response.json().id);
            }, (errors) => {
                alert(error);
            })
        },
        updateSite: ({commit}, data) => {
            Vue.http.put(action('Site\SiteController@update', {site: data.site_id}), data.data).then((response) => {
                commit('SET_SITE', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        updateSiteServerFeatures: ({commit}, data) => {
            $.ajax({
                type: "PUT",
                url: action('Site\SiteController@updateSiteServerFeatures', {site: data.site}),
                data: data.data,
                dataType: "json",
                success: function () {
                    siteStore.dispatch('getSite', data.site);
                },
                error: function () {
                    alert('error');
                }
            });
        },
        deleteSite: ({commit}, site_id) => {
            Vue.http.delete(action('Site\SiteController@destroy', {site: site_id})).then((response) => {
                siteStore.dispatch('getSites');
                app.$router.push('/');
            }, (errors) => {

            })
        },
        getWorkers: ({commit}, site_id) => {
            Vue.http.get(action('Site\SiteWorkerController@show', {site: site_id})).then((response) => {
                commit('SET_WORKERS', response.json());
            }, (errors) => {
            });
        },
        installWorker: ({commit}, data) => {
            Vue.http.post(action('Site\SiteWorkerController@store', {site: data.site_id}), data).then((response) => {
                siteStore.dispatch('getWorkers', data.site_id);
            }, (errors) => {
                alert(error);
            });
        },
        deleteWorker: ({commit}, data) => {
            Vue.http.delete(action('Site\SiteWorkerController@destroy', {
                site: data.site,
                worker: data.worker
            })).then((response) => {
                siteStore.dispatch('getWorkers', data.site);
            }, (errors) => {
            });
        },
        getSslCertificates: ({commit}, site_id) => {
            Vue.http.get(action('Site\Certificate\SiteSSLController@index', {site: site_id})).then((response) => {
                commit('SET_SSL_CERTIFICATES', response.json());
            }, (errors) => {
            });
        },
        installLetsEncryptSslCertificate: ({commit}, data) => {
            Vue.http.post(action('Site\Certificate\SiteSSLLetsEncryptController@store', {site: data.site_id}), data).then((response) => {
                siteStore.dispatch('getSslCertificates', data.site_id);
            }, (errors) => {
            });
        },
        deleteSslCertificate: ({commit}, ssl_certificate_id) => {
            Vue.http.delete(action('Site\Certificate\SiteSSLController@destroy', {ssl: ssl_certificate_id})).then((response) => {
                siteStore.dispatch('getSslCertificates', siteStore.state.site.id);
            }, (errors) => {

            });
        },
        getSiteServers: ({commit}, site_id) => {
            Vue.http.get(action('Site\SiteServerController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_SERVERS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        updateLinkedServers: ({commit}, data) => {
            Vue.http.post(action('Site\SiteServerController@store', {site: data.site}), data).then((response) => {
                siteStore.dispatch('getSiteServers', data.site);
            });
        },
        saveSiteFile: ({commit}, data) => {
            Vue.http.post(laroute.action('Site\SiteFileController@store', {
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
            Vue.http.put(laroute.action('Site\SiteFileController@update', {
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
});

export default siteStore