export default {
    state: {
        server_cron_jobs: []
    },
    actions: {
        getServerCronJobs: ({ commit }, serverId) => {
            Vue.http.get(Vue.action('Server\ServerCronJobController@index', { server: serverId })).then((response) => {
                commit('SET_SERVER_CRON_JOBS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createServerCronJob: ({ commit }, data) => {
            Vue.http.post(Vue.action('Server\ServerCronJobController@store', { server: data.server }), data).then((response) => {
                commit('ADD_SERVER_CRON_JOB', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteServerCronJob: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Server\ServerCronJobController@destroy', {
                server: data.server,
                cron_job: data.cron_job
            })).then(() => {
                commit('REMOVE_SERVER_CRON_JOB', data.cron_job)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SERVER_CRON_JOB: (state, cronJob) => {
            state.server_cron_jobs.push(cronJob)
        },
        REMOVE_SERVER_CRON_JOB: (state, cronJobId) => {
            Vue.set(state, 'server_cron_jobs', _.reject(state.server_cron_jobs, { id: cronJobId }))
        },
        SET_SERVER_CRON_JOBS: (state, serverCronJobs) => {
            state.server_cron_jobs = serverCronJobs
        }
    }
}
