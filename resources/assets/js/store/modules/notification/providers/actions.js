export const get = (context, data) => {
  return Vue.request(data).get(
    Vue.action('AuthProvidersNotificationProvidersController@index'),
    'notification_providers/setAll',
  );
};
