export const get = () => {
  return Vue.request().get(
    Vue.action("CategoriesController@index"),
    "admin_categories/setAll",
  );
};

export const show = (context, category) => {
  return Vue.request().get(
    Vue.action("CategoriesController@show", { category: category }),
    "admin_categories/set",
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action("CategoriesController@store"),
    "admin_categories/add",
  );
};

export const update = (context, data) => {
  return Vue.request(data).put(
    Vue.action("CategoriesController@update", { category: data.category }),
  );
};

export const destroy = (context, category) => {
  return Vue.request(category).delete(
    Vue.action("CategoriesController@update", { category: category }),
    "admin_categories/remove",
  );
};
