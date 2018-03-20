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
  return Vue.request(data).post(
    Vue.action("BittsController@store"),
    "bitts/add",
  );
};

export const update = (context, data) => {
  return Vue.request(data).patch(
    Vue.action("BittsController@update", { bitt: data.bitt }),
    "bitts/update",
  );
};

export const destroy = (context, bitt) => {
  return Vue.request().delete(Vue.action("BittsController@destroy", { bitt }));
};

export const run = (context, data) => {
  return Vue.request(data).post(
    Vue.action("BittsController@run", { bitt: data.bitt }),
  );
};
