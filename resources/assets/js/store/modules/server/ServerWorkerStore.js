export default {
    state: {
        server_workers: []
    },
    actions: {
        getServerWorkers: ({ commit }, server_id) => {
            Vue.http.get(Vue.action('Server\ServerWorkerController@index', { server: server_id })).then((response) => {
                commit('SET_SERVER_WORKERS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createServerWorker: ({ commit }, data) => {
            Vue.http.post(Vue.action('Server\ServerWorkerController@store', { server: data.server }), data).then((response) => {
                commit('ADD_SERVER_WORKER', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteServerWorker: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Server\ServerWorkerController@destroy', {
                server: data.server,
                worker: data.worker
            })).then(() => {
                commit('REMOVE_SERVER_WORKER', data.worker)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SERVER_WORKER: (state, worker) => {
            state.server_workers.push(worker)
        },
        REMOVE_SERVER_WORKER: (state, worker_id) => {
            Vue.set(state, 'server_workers', _.reject(state.server_workers, { id: worker_id }))
        },
        SET_SERVER_WORKERS: (state, server_workers) => {
            state.server_workers = server_workers
        }
    }
}
