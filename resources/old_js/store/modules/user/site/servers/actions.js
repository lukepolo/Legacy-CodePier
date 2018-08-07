export const get = (context, site) => {
  return Vue.request(site).get(
    Vue.action("SiteSiteServerController@index", { site: site }),
    "user_site_servers/setAll",
  );
};

export const updateLinks = ({ dispatch }, data) => {
  return Vue.request(data)
    .post(Vue.action("SiteSiteServerController@store", { site: data.site }))
    .then(() => {
      dispatch("get", data.site);
    });
};
