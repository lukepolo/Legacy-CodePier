export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerWorkerController@index', { server: server }),
        'user_server_workers/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerWorkerController@store', { server: data.server }),
        'user_server_workers/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerWorkerController@destroy', {
            server: data.server,
            worker: data.worker
        }),
        'user_server_workers/remove'
    )
}
