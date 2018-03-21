export const get = () => {
  return Vue.request().get(Vue.action("BittsController@index"), "bitts/setAll");
};

export const show = (context, bitt) => {
  return Vue.request().get(
    Vue.action("BittsController@show", { bitt: bitt }),
    "bitts/set",
  );
};

export const store = (context, data) => {
  return Vue.request(data)
    .post(Vue.action("BittsController@store"), "bitts/add")
    .then((response) => {
      return response;
    });
};

export const update = (context, { bitt, form }) => {
  return Vue.request(form)
    .patch(Vue.action("BittsController@update", { bitt }), "bitts/update")
    .then((response) => {
      app.showSuccess("You have updated your bitt");
      return response;
    });
};

export const destroy = (context, bitt) => {
  return Vue.request().delete(Vue.action("BittsController@destroy", { bitt }));
};

export const run = (context, data) => {
  return Vue.request(data).post(
    Vue.action("BittsController@run", { bitt: data.bitt }),
  );
};
