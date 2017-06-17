export const get = (context, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerBuoyController@index', { server: server }),
        'user_server_buoys/set'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerBuoyController@destroy', {
            buoy: data.buoy,
            server: data.server
        }),
        'user_server_buoys/remove'
    )
}

export const all = () => {
    return Vue.request().get(
        Vue.action('Server\ServerBuoyController@myServerBuoys'),
        'user_server_buoys/all'
    )
}