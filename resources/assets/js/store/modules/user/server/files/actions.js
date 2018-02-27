export const get = (context, server) => {
  return Vue.request().get(
    Vue.action("ServerServerFileController@index", { server: server }),
    "user_server_files/setAll",
  );
};

export const update = (context, data) => {
  return Vue.request(data)
    .patch(
      Vue.action("ServerServerFileController@update", {
        file: data.file_id,
        server: data.server,
      }),
      "user_server_files/update",
    )
    .then(() => {
      app.showSuccess("You have updated the file");
    });
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action("ServerServerFileController@destroy", {
      server: data.server,
      file: data.file,
    }),
    "user_server_files/remove",
  );
};

export const find = (context, data) => {
  return Vue.request(data).post(
    Vue.action("ServerServerFileController@find", { server: data.server }),
    "user_server_files/add",
  );
};

export const reload = (context, data) => {
  return Vue.request()
    .post(
      Vue.action("ServerServerFileController@reloadFile", {
        file: data.file,
        server: data.server,
      }),
      "user_server_files/update",
    )
    .then(() => {
      app.showSuccess("You have reloaded the server file.");
    });
};
