export default {
    actions: {
        restartServer: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartServer', { server: server })).then((response) => {

            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartServerWebServices: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartWebServices', { server: server })).then((response) => {

            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartServerDatabases: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartDatabases', { server: server })).then((response) => {

            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restartServerWorkers: ({}, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restartWorkerServices', { server: server })).then((response) => {

            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    }
}
