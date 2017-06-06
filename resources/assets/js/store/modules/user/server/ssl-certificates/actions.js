export const get = (context, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerSslController@index', { server: server }),
        'user_server_ssl_certificates/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerSslController@store', { server: data.server_id }),
        'user_server_ssl_certificates/add'
    )
}

export const update = (context, data) => {
    return Vue.request(data).patch(
        Vue.action('Server\ServerSslController@update', { server: data.server, ssl_certificate: data.ssl_certificate }),
        'user_server_ssl_certificates/update'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerSslController@destroy', { server: data.server, ssl_certificate: data.ssl_certificate }),
        'user_server_ssl_certificates/remove'
    )
}
