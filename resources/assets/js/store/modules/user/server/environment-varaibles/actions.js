export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerEnvironmentVariablesController@index', { server: server }),
        'user_server_environment_variables/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerEnvironmentVariablesController@store', { server: data.server }),
        'user_server_environment_variables/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerEnvironmentVariablesController@destroy', {
            server: data.server,
            environment_variable: data.environment_variable
        }),
        'user_server_environment_variables/remove'
    )
}
