export default {
    actions: {
        restartServer: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartServer', { server: server })).then((response) => {
                app.showSuccess('You have restarted your server')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartServerWebServices: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartWebServices', { server: server })).then((response) => {
                app.showSuccess('You have restarted your web services')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartServerDatabases: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartDatabases', { server: server })).then((response) => {
                app.showSuccess('You have restarted your databases')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartServerWorkers: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartWorkerServices', { server: server })).then((response) => {
                app.showSuccess('You have restarted your workers')
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    }
}
