export const get = (context, site) => {
  return Vue.request().get(
    Vue.action("SiteSiteSchemaBackupsController@index", { site: site }),
    "user_site_schemaBackups/setAll",
  );
};

export const download = (context, { site, backup }) => {
  return Vue.request()
    .get(
      Vue.action("SiteSiteSchemaBackupsController@show", {
        site,
        schemaBackup: backup,
      }),
    )
    .then((downloadLink) => {
      let link = document.createElement("a");
      link.href = downloadLink;
      link.click();
      link.remove();
    });
};
