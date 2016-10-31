export default {
    state: {
        ssl_certificates: []
    },
    actions: {
        getSslCertificates: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\Certificate\SiteSSLController@index', {site: site_id})).then((response) => {
                commit('SET_SSL_CERTIFICATES', response.data);
            }, (errors) => {
            });
        },
        installLetsEncryptSslCertificate: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\Certificate\SiteSSLLetsEncryptController@store', {site: data.site_id}), data).then((response) => {
                dispatch('getSslCertificates', data.site_id);
            }, (errors) => {
            });
        },
        deleteSslCertificate: ({commit, rootState, dispatch}, data) => {
            Vue.http.delete(Vue.action('Site\Certificate\SiteSSLController@destroy', {site : data.site, certificate: data.certificate})).then((response) => {
                dispatch('getSslCertificates', rootState.sitesStore.site.id);
            }, (errors) => {

            });
        },
    },
    mutations: {
        SET_SSL_CERTIFICATES: (state, ssl_certificates) => {
            state.ssl_certificates = ssl_certificates;
        }
    }
}