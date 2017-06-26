export const get = () => {
  return Vue.request().get(Vue.action('UserUserController@index'), 'user/set');
};

export const update = (context, data) => {
  return Vue.request(data).patch(
    Vue.action('UserUserController@update', { user: data.user }),
    'user/set',
  );
};
