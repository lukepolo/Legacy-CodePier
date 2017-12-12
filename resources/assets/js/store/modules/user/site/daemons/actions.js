export const get = (context, site) => {
  return Vue.request().get(
    Vue.action('SiteSiteDaemonsController@index', { site: site }),
    'user_site_daemons/setAll'
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action('SiteSiteDaemonsController@store', { site: data.site }),
    'user_site_daemons/add'
  );
};

export const patch = (context, data) => {
  return Vue.request(data).put(
    Vue.action('SiteSiteDaemonsController@update', {
      site: data.site,
      daemon: data.daemon
    }),
    'user_site_daemons/update'
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action('SiteSiteDaemonsController@destroy', {
      site: data.site,
      daemon: data.daemon
    }),
    'user_site_daemons/remove'
  );
};
