export const get = (context, site) => {
  return Vue.request().get(
    Vue.action("Site\SiteSchemaUserController@index", { site: site }),
    "user_site_schema_users/setAll"
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action("Site\SiteSchemaUserController@store", { site: data.site }),
    "user_site_schema_users/add"
  );
};

// TODO - there will be an update

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action("Site\SiteSchemaUserController@destroy", {
      site: data.site,
      schema_user: data.schema_user
    }),
    "user_site_schema_users/remove"
  );
};
