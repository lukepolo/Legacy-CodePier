export const get = (context, server) => {
  return Vue.request().get(
    Vue.action("ServerServerLanguageSettingsController@index", {
      server: server
    }),
    "user_server_language_settings/setAll"
  );
};

export const getAvailable = (context, server) => {
  Vue.request().get(
    Vue.action("ServerServerLanguageSettingsController@getLanguageSettings", {
      server: server
    }),
    "user_server_language_settings/setAvailableLanguageSettings"
  );
};

export const run = (context, data) => {
  Vue.request(data)
    .post(
      Vue.action("ServerServerLanguageSettingsController@store", {
        server: data.server
      })
    )
    .then(() => {
      app.showSuccess("You have updated the settings");
    });
};
