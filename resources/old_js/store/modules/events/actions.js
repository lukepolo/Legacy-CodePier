export const get = (context, data) => {
  // page: data ? data.page : 1,

  return Vue.request(filters).post(
    Vue.action("EventController@store"),
    "events/setAll",
  );
};
