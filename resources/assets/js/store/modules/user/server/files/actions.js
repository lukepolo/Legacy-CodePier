export const get = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerFileController@index', { server: server }),
        'user_server_files/setAll'
    )
}

export const update = ({}, data) => {
    return Vue.request(data).patch(
        Vue.action('Server\ServerFileController@update', {
            file: data.file_id,
            server: data.server
        }),
        'user_server_files/update'
    ).then(() => {
        app.showSuccess('You have updated the file')
    })
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete('')
}

export const getEditableFiles = ({}, server) => {
    Vue.request().get(
        Vue.action('Server\ServerFeatureController@getEditableFiles', { server: server }),
        'user_server_files/setEditableFiles'
    ).then(() => {
        app.showSuccess('You have updated the server file.')
    })
}

export const find = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerFileController@find', { server: data.server }),
        'user_server_files/add'
    )
}

export const reload = ({}, data) => {
    return Vue.request().post(
        Vue.action('Server\ServerFileController@reloadFile', {
            file: data.file,
            server: data.server
        }),
        'user_server_files/update'
    ).then(() => {
        app.showSuccess('You have reloaded the server file.')
    })
}
