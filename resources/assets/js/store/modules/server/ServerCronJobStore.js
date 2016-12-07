export default {
    state: {
        server_cron_jobs: [],
    },
    actions: {
        getServerCronJobs: ({commit}, server_id) => {
            Vue.http.get(Vue.action('Server\ServerCronJobController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_CRON_JOBS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        createServerCronJob: ({dispatch}, data) => {
            Vue.http.post(Vue.action('Server\ServerCronJobController@store', {server: data.server}), data).then(() => {
                dispatch('getServerCronJobs', data.server);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteServerCronJob: ({dispatch}, data) => {
            Vue.http.delete(Vue.action('Server\ServerCronJobController@destroy', {
                server: data.server,
                cron_job: data.cron_job
            })).then(() => {
                dispatch('getServerCronJobs', data.server);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        SET_SERVER_CRON_JOBS: (state, server_cron_jobs) => {
            state.server_cron_jobs = server_cron_jobs;
        }
    }
}