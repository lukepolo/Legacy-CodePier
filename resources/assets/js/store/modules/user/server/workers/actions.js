export const get = (context, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerWorkerController@index', { server: server }),
        'user_server_workers/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerWorkerController@store', { server: data.server }),
        'user_server_workers/add'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerWorkerController@destroy', {
            server: data.server,
            worker: data.worker
        }),
        'user_server_workers/remove'
    )
}
