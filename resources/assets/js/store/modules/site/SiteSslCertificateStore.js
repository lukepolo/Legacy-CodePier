export default {
    state: {
        ssl_certificates: []
    },
    actions: {
        getSslCertificates: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteSslController@index', {site: site_id})).then((response) => {
                commit('SET_SSL_CERTIFICATES', response.data);
            }, (errors) => {
            });
        },
        installSslCertificate: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteSslController@store', {site: data.site_id}), data).then((response) => {
                dispatch('getSslCertificates', data.site_id);
            }, (errors) => {
            });
        },
        deleteSslCertificate: ({commit, rootState, dispatch}, data) => {
            Vue.http.delete(Vue.action('Site\SiteSslController@destroy', {site : data.site, certificate: data.certificate})).then((response) => {
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