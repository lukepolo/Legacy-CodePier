import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./../helpers";

Vue.use(Vuex);

const serverServicesStore = new Vuex.Store({
    actions: {
        restartServer: ({}, server_id) => {
            Vue.http.post(action('Server\ServerController@restartServer', {server: server_id})).then((response) => {

            }, (errors) => {
                alert(error);
            });
        },
        restartServerWebServices: ({}, server_id) => {
            Vue.http.post(action('Server\ServerController@restartWebServices', {server: server_id})).then((response) => {

            }, (errors) => {
                alert(error);
            });
        },
        restartServerDatabases: ({}, server_id) => {
            Vue.http.post(action('Server\ServerController@restartDatabases', {server: server_id})).then((response) => {

            }, (errors) => {
                alert(error);
            });
        },
        restartServerWorkers: ({}, server_id) => {
            Vue.http.post(action('Server\ServerController@restartWorkerServices', {server: server_id})).then((response) => {

            }, (errors) => {
                alert(error);
            });
        },
    }
});

export default serverServicesStore