export const get = () => {
  return Vue.request().get(Vue.action("UserUserController@index"), "user/set");
};

export const update = (context, data) => {
  return Vue.request(data).patch(
    Vue.action("UserUserController@update", { user: data.user }),
    "user/set"
  );
};

export const resendConfirmation = () => {
  return Vue.request().post(
    Vue.action("UserUserConfirmController@store"),
  ).then((response) => {
    app.showSuccess('We have sent you another confirmation email');
    return response;
  });
};
