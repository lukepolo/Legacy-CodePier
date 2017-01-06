export default {
  state: {
    ssl_certificates: []
  },
  actions: {
    getSslCertificates: ({ commit }, site_id) => {
      Vue.http.get(Vue.action('Site\SiteSslController@index', { site: site_id })).then((response) => {
        commit('SET_SITE_SSL_CERTIFICATES', response.data)
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
        alert('need to do update ssl cert')
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
    }
  },
  mutations: {
    ADD_SITE_SSL_CERTIFICATE: (state, ssl_certificate) => {
      state.ssl_certificates.push(ssl_certificate)
    },
    REMOVE_SITE_SSL_CERTIFICATE: (state, ssl_certificate_id) => {
      Vue.set(state, 'ssl_certificates', _.reject(state.ssl_certificates, { id: ssl_certificate_id }))
    },
    SET_SITE_SSL_CERTIFICATES: (state, ssl_certificates) => {
      state.ssl_certificates = ssl_certificates
    }
  }
}
