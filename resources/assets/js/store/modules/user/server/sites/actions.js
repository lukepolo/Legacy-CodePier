export const get = (context, server) => {
  return Vue.request().get(
    Vue.action("ServerServerSiteController@index", { server: server }),
    "user_server_sites/setAll",
  );
};
