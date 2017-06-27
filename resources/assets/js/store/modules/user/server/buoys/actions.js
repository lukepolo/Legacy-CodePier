export const get = (context, server) => {
    return Vue.request().get(
        Vue.action("ServerServerBuoyController@index", { server: server }),
        "user_server_buoys/setAll"
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action("ServerServerBuoyController@destroy", {
            buoy: data.buoy,
            server: data.server
        }),
        "user_server_buoys/remove"
    );
};
