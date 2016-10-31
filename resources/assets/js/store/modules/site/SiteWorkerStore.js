export default {
    state: {
        workers: [],
    },
    actions: {
        getWorkers: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteWorkerController@show', {site: site_id})).then((response) => {
                commit('SET_WORKERS', response.data);
            }, (errors) => {
            });
        },
        installWorker: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteWorkerController@store', {site: data.site_id}), data).then((response) => {
                dispatch('getWorkers', data.site_id);
            }, (errors) => {
                alert(error);
            });
        },
        deleteWorker: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('Site\SiteWorkerController@destroy', {
                site: data.site,
                worker: data.worker
            })).then((response) => {
                dispatch('getWorkers', data.site);
            }, (errors) => {
            });
        },
    },
    mutations: {
        SET_WORKERS: (state, workers) => {
            state.workers = workers;
        }
    }
}