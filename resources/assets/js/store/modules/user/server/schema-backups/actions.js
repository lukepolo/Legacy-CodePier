export const get = (context, server) => {
  return Vue.request().get(
    Vue.action("ServerServerSchemaBackupsController@index", { server }),
  );
};

export const enable = (context, server) => {
  return Vue.request({ enabled: true })
    .post(Vue.action("ServerServerSchemaBackupsController@store", { server }), [
      "user_servers/update",
      "user_site_servers/update",
    ])
    .then(() => {
      app.showSuccess("You have enabled backups for the server");
    });
};

export const disable = (context, server) => {
  return Vue.request({ enabled: false })
    .post(Vue.action("ServerServerSchemaBackupsController@store", { server }), [
      "user_servers/update",
      "user_site_servers/update",
    ])
    .then(() => {
      app.showSuccess("You have enabled backups for the server");
    });
};

export const restore = (context, { server, backup }) => {
  return Vue.request()
    .post(
      Vue.action("ServerServerRestoreSchemaBackupsController@store", {
        server,
        schemaBackup: backup,
      }),
    )
    .then(() => {
      app.showSuccess("You have started the restore");
    });
};

export const download = (context, { server, backup }) => {
  return Vue.request()
    .get(
      Vue.action("ServerServerSchemaBackupsController@show", {
        server,
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
