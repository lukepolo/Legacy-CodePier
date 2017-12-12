export const get = (context, site) => {
  return Vue.request().get(
    Vue.action('SiteSiteFileController@index', { site: site }),
    'user_site_files/setAll'
  );
};

export const update = (context, data) => {
  return Vue.request(data)
    .patch(
      Vue.action('SiteSiteFileController@update', {
        site: data.site,
        file: data.file_id
      }),
      'user_site_files/update'
    )
    .then(() => {
      app.showSuccess('You have updated the file');
    });
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action('SiteSiteFileController@destroy', {
      site: data.site,
      file: data.file
    }),
    'user_site_files/remove'
  );
};

export const find = (context, data) => {
  return Vue.request(data).post(
    Vue.action('SiteSiteFileController@find', {
      site: data.site
    }),
    'user_site_files/add'
  );
};

export const reload = (context, data) => {
  return Vue.request(data)
    .post(
      Vue.action('SiteSiteFileController@reloadFile', {
        site: data.site,
        file: data.file,
        server: data.server
      }),
      'user_site_files/update'
    )
    .then(() => {
      app.showSuccess('You have reloaded the file');
    });
};
