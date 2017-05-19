export const restart = ({}, server) => {
    Vue.request().post(
        Vue.action('Server\ServerController@restartServer', { server: server })
    ).then(() => {
        app.showSuccess('You have restarted your server')
    })
}
export const restartWebServices = ({}, server) => {
    Vue.request().post(
        Vue.action('Server\ServerController@restartWebServices', { server: server })
    ).then(() => {
        app.showSuccess('You have restarted your web services')
    })
}

export const restartDatabases = ({}, server) => {
    Vue.request().post(
        Vue.action('Server\ServerController@restartDatabases', { server: server })
    ).then(() => {
        app.showSuccess('You have restarted your databases')
    })
}

export const restartWorkers = ({}, server) => {
    Vue.request().post(
        Vue.action('Server\ServerController@restartWorkerServices', {server: server})
    ).then(() => {
        app.showSuccess('You have restarted your workers')
    })
}