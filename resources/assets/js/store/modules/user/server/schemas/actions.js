export const get = (context, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerSchemaController@index', { server: server }),
        'user_server_schemas/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerSchemaController@store', { server: data.server }),
        'user_server_schemas/add'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerSchemaController@destroy', {
            server: data.server,
            schema: data.schema
        }),
        'user_server_schemas/remove'
    )
}
