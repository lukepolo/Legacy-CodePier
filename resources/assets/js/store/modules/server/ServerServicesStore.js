export default {
    actions: {
        restartServer: ({}, server_id) => {
            Vue.http.post(Vue.action('Server\ServerController@restartServer', {server: server_id})).then((response) => {

            }, (errors) => {
                app.showError(errors);
            });
        },
        restartServerWebServices: ({}, server_id) => {
            Vue.http.post(Vue.action('Server\ServerController@restartWebServices', {server: server_id})).then((response) => {

            }, (errors) => {
                app.showError(errors);
            });
        },
        restartServerDatabases: ({}, server_id) => {
            Vue.http.post(Vue.action('Server\ServerController@restartDatabases', {server: server_id})).then((response) => {

            }, (errors) => {
                app.showError(errors);
            });
        },
        restartServerWorkers: ({}, server_id) => {
            Vue.http.post(Vue.action('Server\ServerController@restartWorkerServices', {server: server_id})).then((response) => {

            }, (errors) => {
                app.showError(errors);
            });
        },
    }
}