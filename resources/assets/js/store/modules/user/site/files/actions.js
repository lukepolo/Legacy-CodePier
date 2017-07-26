export const get = (context, site) => {
  return Vue.request().get(
    Vue.action("SiteSiteFileController@index", { site: site }),
    "user_site_files/setAll"
  );
};

export const update = (context, data) => {
  return Vue.request(data)
    .patch(
      Vue.action("SiteSiteFileController@update", {
        site: data.site,
        file: data.file_id
      }),
      "user_site_files/update"
    )
    .then(() => {
      app.showSuccess("You have updated the file");
    });
};

export const destroy = (context, data) => {
  return Vue.request(data).delete("");
};

export const getEditableFiles = (context, site) => {
  Vue.request().get(
    Vue.action("SiteSiteFeatureController@getEditableFiles", {
      site: site
    }),
    "user_site_files/setEditableFiles"
  );
};

export const getEditableFrameworkFiles = (context, site) => {
  return Vue.request().get(
    Vue.action("SiteSiteFeatureController@getEditableFrameworkFiles", {
      site: site
    }),
    "user_site_files/setEditableFrameworkFiles"
  );
};

export const find = (context, data) => {
  return Vue.request(data).post(
    Vue.action("SiteSiteFileController@find", {
      site: data.site
    }),
    "user_site_files/add"
  );
};

export const reload = (context, data) => {
  return Vue.request(data)
    .post(
      Vue.action("SiteSiteFileController@reloadFile", {
        site: data.site,
        file: data.file,
        server: data.server
      }),
      "user_site_files/update"
    )
    .then(() => {
      app.showSuccess("You have reloaded the file");
    });
};

//     addCustomFile: ({ commit }, site) => {
//     Vue.http.get().then((response) => {
//         commit('ADD_SITE_FILE', response.data)
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//
//
// },
//
