export default {
    state: {
        site_cron_jobs: [],
    },
    actions: {
        getSiteCronJobs: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteCronJobController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_CRON_JOBS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        createSiteCronJob: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteCronJobController@store', {site: data.site}), data).then((response) => {
                commit('ADD_SITE_CRON_JOB', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteSiteCronJob: ({commit}, data) => {
            Vue.http.delete(Vue.action('Site\SiteCronJobController@destroy', {
                site: data.site,
                cron_job: data.cron_job
            })).then(() => {
                commit('REMOVE_SITE_CRON_JOB', response.cron_job);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        ADD_SITE_CRON_JOB: (state, cron_job) => {
            state.site_cron_jobs.push(cron_job);
        },
        REMOVE_SITE_CRON_JOB : (state, cron_job_id) => {
            Vue.set(state, 'site_cron_jobs', _.reject(state.site_cron_jobs, { id : cron_job_id }));
        },
        SET_SITE_CRON_JOBS: (state, site_cron_jobs) => {
            state.site_cron_jobs = site_cron_jobs;
        }
    }
}