export const get = () => {
  return Vue.request().get(
    Vue.action("UserUserSslCertificates@index"),
    "user_ssl_certificates/setAll",
  );
};
