export default {
    state: {
        ssl_certificates: []
    },
    actions: {
        getSslCertificates: ({ commit, dispatch }, siteId) => {
            Vue.http.get(Vue.action('Site\SiteSslController@index', { site: siteId })).then((response) => {
                commit('SET_SITE_SSL_CERTIFICATES', response.data)
                _.each(response.data, (sslCertificate) => [
                    dispatch('listenToSslCertificate', sslCertificate)
                ])
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        installSslCertificate: ({ commit }, data) => {
            Vue.http.post(Vue.action('Site\SiteSslController@store', { site: data.site_id }), data).then((response) => {
                commit('ADD_SITE_SSL_CERTIFICATE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateSslCertificate: ({ commit }, data) => {
            Vue.http.put(Vue.action('Site\SiteSslController@update', { site: data.site, ssl_certificate: data.ssl_certificate }), data).then((response) => {
                commit('UPDATE_SITE_SSL_CERTIFICATE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteSslCertificate: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Site\SiteSslController@destroy', { site: data.site, ssl_certificate: data.ssl_certificate })).then((response) => {
                commit('REMOVE_SITE_SSL_CERTIFICATE', data.ssl_certificate)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        listenToSslCertificate: ({ commit }, sslCertificate) => {
            Echo.private('App.Models.SslCertificate.' + sslCertificate.id)
                .listen('SslCertificate\\SslCertificateUpdated', (data) => {
                    alert('site ssl')
                    commit('UPDATE_SITE_SSL_CERTIFICATE', data)
                })
        }
    },
    mutations: {
        ADD_SITE_SSL_CERTIFICATE: (state, sslCertificate) => {
            state.ssl_certificates.push(sslCertificate)
        },
        UPDATE_SITE_SSL_CERTIFICATE: (state, sslCertificate) => {
            const siteSslCertificateKey = _.findKey(state.ssl_certificates, { id: sslCertificate.id })
            if (siteSslCertificateKey) {
                Vue.set(state[ssl_certificates], siteSslCertificateKey, sslCertificate)
            }
        },
        REMOVE_SITE_SSL_CERTIFICATE: (state, sslCertificate) => {
            Vue.set(state, 'ssl_certificates', _.reject(state.ssl_certificates, { id: sslCertificate }))
        },
        SET_SITE_SSL_CERTIFICATES: (state, sslCertificates) => {
            state.ssl_certificates = sslCertificates
        }
    }
}
