export default {
    state: {
        server_cron_jobs: [],
    },
    actions: {
        getServerCronJobs: ({commit}, server_id) => {
            Vue.http.get(action('Server\ServerCronJobController@index', {server: server_id})).then((response) => {
                commit('SET_SERVER_CRON_JOBS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createServerCronJob: ({commit}, data) => {
            Vue.http.post(action('Server\ServerCronJobController@store', {server: data.server}), data).then(() => {
                serverCronJobStore.dispatch('getServerCronJobs', data.server);
            }, (errors) => {
                alert(error);
            });
        },
        deleteServerCronJob: ({commit}, data) => {
            Vue.http.delete(action('Server\ServerCronJobController@destroy', {
                server: data.server,
                cron_job: data.cron_job
            })).then(() => {
                serverCronJobStore.dispatch('getServerCronJobs', data.server);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SERVER_CRON_JOBS: (state, server_cron_jobs) => {
            state.server_cron_jobs = server_cron_jobs;
        }
    }
}