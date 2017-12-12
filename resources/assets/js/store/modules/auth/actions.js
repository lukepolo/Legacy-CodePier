export const logout = (context, data) => {
  return Vue.request(data)
    .post(Vue.action('AuthLoginController@logout'))
    .then(() => {
      window.location = '/';
    });
};

export const getSecondAuthQr = () => {
  return Vue.request().get(Vue.action('AuthSecondAuthController@index'));
};

export const validateSecondAuth = (context, token) => {
  return Vue.request({
    token: token
  }).post(Vue.action('AuthSecondAuthController@store'), 'user/set');
};
