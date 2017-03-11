export default {
    state: {
        ssl_certificates: []
    },
    actions: {
        getServerSslCertificates: ({ commit, dispatch }, serverId) => {
            Vue.http.get(Vue.action('Server\ServerSslController@index', { server: serverId })).then((response) => {
                commit('SET_SERVER_SSL_CERTIFICATES', response.data)

                _.each(response.data, (sslCertificate) => [
                    dispatch('listenToSslCertificate', sslCertificate)
                ])
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        installServerSslCertificate: ({ commit }, data) => {
            return Vue.http.post(Vue.action('Server\ServerSslController@store', { server: data.server_id }), data).then((response) => {

                if (_.isObject(response.data)) {
                    commit('UPDATE_SERVER_SSL_CERTIFICATE', response.data)
                }

                return response.data

            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteServerSslCertificate: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Server\ServerSslController@destroy', { server: data.server, ssl_certificate: data.ssl_certificate })).then((response) => {
                commit('REMOVE_SERVER_SSL_CERTIFICATE', data.ssl_certificate)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        UPDATE_SERVER_SSL_CERTIFICATE: (state, sslCertificate) => {
            const serverSslCertificateKey = _.findKey(state.ssl_certificates, { id: sslCertificate.id })
            if (serverSslCertificateKey) {
                Vue.set(state.ssl_certificates, serverSslCertificateKey, sslCertificate)
            } else {
                app.$store.dispatch('listenToSslCertificate', sslCertificate)
                state.ssl_certificates.push(sslCertificate)
            }
        },
        REMOVE_SERVER_SSL_CERTIFICATE: (state, sslCertificate) => {
            Vue.set(state, 'ssl_certificates', _.reject(state.ssl_certificates, { id: sslCertificate }))
        },
        SET_SERVER_SSL_CERTIFICATES: (state, sslCertificates) => {
            state.ssl_certificates = sslCertificates
        }
    }
}
