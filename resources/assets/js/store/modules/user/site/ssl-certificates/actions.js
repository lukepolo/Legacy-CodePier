export const get = ({ dispatch }, site) => {
  return Vue.request()
    .get(
      Vue.action("SiteSiteSslController@index", { site: site }),
      "user_site_ssl_certificates/setAll"
    )
    .then(sslCertificates => {
      _.each(sslCertificates, sslCertificate => {
        dispatch("listenToSslCertificate", sslCertificate);
      });
      return sslCertificates;
    });
};

export const store = ({ dispatch }, data) => {
  return Vue.request(data)
    .post(
      Vue.action("SiteSiteSslController@store", { site: data.site_id }),
      "user_site_ssl_certificates/add"
    )
    .then(sslCertificate => {
      if (sslCertificate !== "OK") {
        dispatch("listenToSslCertificate", sslCertificate);
        return sslCertificate;
      }
    });
};

export const update = (context, data) => {
  return Vue.request(data).patch(
    Vue.action("SiteSiteSslController@update", {
      site: data.site,
      ssl_certificate: data.ssl_certificate
    }),
    "user_site_ssl_certificates/update"
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action("SiteSiteSslController@destroy", {
      site: data.site,
      ssl_certificate: data.ssl_certificate
    }),
    "user_site_ssl_certificates/remove"
  );
};

export const listenToSslCertificate = ({ commit }, sslCertificate) => {
  Echo.private(
    "App.Models.SslCertificate." + sslCertificate.id
  ).listen("SslCertificate\\SslCertificateUpdated", data => {
    commit("update", {
      response: data.ssl_certificate
    });
  });
};
