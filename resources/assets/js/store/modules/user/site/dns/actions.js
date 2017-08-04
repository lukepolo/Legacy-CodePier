export const get = (context, data) => {
  return Vue.request(data).get(
    Vue.action("Site\SiteDnsController@index", { site: data.site }),
    "user_site_dns/setAll"
  );
};
