export const logout = (context, data) => {
  return Vue.request(data)
    .post(Vue.action("AuthLoginController@logout"))
    .then(() => {
      let location = window.location;
      window.location = location.host.replace("app.", `${location.protocol}//`);
    });
};

export const getSecondAuthQr = () => {
  return Vue.request().get(Vue.action("AuthSecondAuthController@index"));
};

export const validateSecondAuth = (context, token) => {
  return Vue.request({
    token: token,
  }).post(Vue.action("AuthSecondAuthController@store"), "user/set");
};
