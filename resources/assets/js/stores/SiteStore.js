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
        getSite: ({commit}, site_id) => {
            Vue.http.get(action('Site\SiteController@show', {site: site_id})).then((response) => {
                commit('SET_SITE', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        getSites: ({commit}) => {
            Vue.http.get(action('Pile\PileSitesController@show', {pile: pileStore.state.current_pile_id})).then((response) => {
                commit('SET_SITES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        createSite : ({commit}, payload) => {
            Vue.http.post(action('Site\SiteController@store'), {
                domain: payload.domain,
                servers: payload.selectedServers,
                wildcard_domain: payload.wildcard_domain,
                pile_id: pileStore.state.current_pile_id
            }).then((response) => {
                app.$router.push('/site/ ' + response.json().id);
            }, (errors) => {
                alert(error);
            })
        },
        updateSite: ({commit}, payload) => {
            Vue.http.put(action('Site\SiteController@update', {site: payload.site_id}), payload.data).then((response) => {
                commit('SET_SITE', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        deleteSite: ({commit}, site_id) => {
            Vue.http.delete(action('Site\SiteController@destroy', {site: site_id})).then((response) => {
                siteStore.dispatch('getSites');
                app.$router.push('/');
            }, (errors) => {
                alert(error);
            })
        },
        getWorkers: ({commit}, site_id) => {
            Vue.http.get(action('Site\Features\SiteWorkerController@show', {site: site_id})).then((response) => {
                commit('SET_WORKERS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        installWorker: ({commit}, payload) => {
            Vue.http.post(action('Site\Features\SiteWorkerController@store', {site: payload.site_id}), payload).then((response) => {
                siteStore.dispatch('getWorkers', payload.site_id);
            }, (errors) => {
                alert(error);
            });
        },
        deleteWorker: ({commit}, worker_id) => {
            Vue.http.delete(action('Site\Features\SiteWorkerController@destroy', {worker: worker_id})).then((response) => {
                siteStore.dispatch('getWorkers', siteStore.state.site.id);
            }, (errors) => {
                alert(error);
            });
        },
        getSslCertificates: ({commit}, site_id) => {
            Vue.http.get(action('Site\Features\SSL\SiteSSLController@index', {site: site_id})).then((response) => {
                commit('SET_SSL_CERTIFICATES', response.json());
            }, (errors) => {
                alert(error);
            });
        },
        installLetsEncryptSslCertificate: ({commit}, payload) => {
            Vue.http.post(action('Site\Features\SSL\SiteSSLLetsEncryptController@store', {site: payload.site_id}), payload).then((response) => {
                siteStore.dispatch('getSslCertificates', payload.site_id);
            }, (errors) => {
                alert(error);
            });
        },
        deleteSslCertificate: ({commit}, ssl_certificate_id) => {
            Vue.http.delete(action('Site\Features\SSL\SiteSSLController@destroy', {ssl: ssl_certificate_id})).then((response) => {
                siteStore.dispatch('getSslCertificates', siteStore.state.site.id);
            }, (errors) => {
                alert(error);
            });
        },
        getSiteServers: ({commit}, site_id) => {
            Vue.http.get(action('Site\SiteServerController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_SERVERS', response.json());
            }, (errors) => {
                alert(error);
            });
        },
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