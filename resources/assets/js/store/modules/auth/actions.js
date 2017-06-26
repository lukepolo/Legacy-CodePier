export const logout = (context, data) => {
  return Vue.request(data)
    .post(Vue.action('AuthLoginController@logout'))
    .then(() => {
      window.location = '/';
    });
};
