export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerFeatureController@index', { server: server }),
        'user_server_features/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerFeatureController@store', { server: data.server }),
        'user_server_features/setAll'
    )
}

export const install = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerFeatureController@store', { server: data.server }), {
            service: data.service,
            feature: data.feature,
            parameters: data.parameters
        }
    ).then(() => {
        app.showSuccess('You have queued a server feature install')
    })
}
