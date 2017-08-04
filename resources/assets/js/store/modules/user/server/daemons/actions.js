export const get = (context, site) => {
  return Vue.request().get(
    Vue.action("Site\SiteDaemonsController@index", { site: site }),
    "user_site_daemons/setAll"
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action("Site\SiteDaemonsController@store", { site: data.site }),
    "user_site_daemons/add"
  );
};

export const patch = data => {
  return Vue.request(data).put(
    Vue.action("Site\SiteDaemonsController@update", { site: data.site }),
    "user_site_daemons/update"
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action("Site\SiteDaemonsController@destroy", {
      site: data.site,
      daemons: data.daemons
    }),
    "user_site_daemons/remove"
  );
};
