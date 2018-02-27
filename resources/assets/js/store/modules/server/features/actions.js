export const get = () => {
  return Vue.request().get(
    Vue.action("ServerServerFeatureController@getFeatures"),
    "server_features/setAll",
  );
};
