export const get = (context, server) => {
  return Vue.request().get(
    Vue.action("ServerServerStatController@index", { server: server }),
    "user_server_stats/set",
  );
};
