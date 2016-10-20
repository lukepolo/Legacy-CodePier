export default {
    state: {
        server_workers: [],
    },
    actions: {
        getServerWorkers: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerWorkerController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_WORKERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createServerWorker: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Server\ServerWorkerController@store', {server: data.server}), data).then((response) => {
                dispatch('getServerWorkers', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        deleteServerWorker: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('Server\ServerWorkerController@destroy', {
                server: data.server,
                worker: data.worker
            })).then((response) => {
                dispatch('getServerWorkers', data.server);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER_WORKERS: (state, server_workers) => {
            state.server_workers = server_workers;
        }
    }
}