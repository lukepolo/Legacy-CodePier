export const get = (context, site) => {
  return Vue.request().get(
    Vue.action("SiteSiteSchemaBackupsController@index", { site: site }),
    "user_site_schema_backups/setAll",
  );
};

export const backupNow = (context, site) => {
  return Vue.request().post(
    Vue.action("SiteSiteSchemaBackupsController@store", { site: site }),
  );
};
