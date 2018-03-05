export const get = () => {
  return Vue.request().get(
    Vue.action("UserUserNotificationSettingsController@index"),
    "user_notification_settings/setAll",
  );
};

export const update = (context, data) => {
  return Vue.request(data).post(
    Vue.action("UserUserNotificationSettingsController@store"),
    "user_notification_settings/setAll",
  );
};
