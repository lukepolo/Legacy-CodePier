export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerBuoyController@index', { server: server }),
        'user_server_buoys/setAll'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerBuoyController@destroy', {
            buoy: data.buoy,
            server: data.server
        }),
        'user_server_buoys/remove'
    )
}
