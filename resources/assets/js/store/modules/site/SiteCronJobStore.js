export default {
    state: {
        site_cron_jobs: [],
    },
    actions: {
        getSiteCronJobs: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteCronJobController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_CRON_JOBS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createSiteCronJob: ({dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteCronJobController@store', {site: data.site}), data).then(() => {
                dispatch('getSiteCronJobs', data.site);
            }, (errors) => {
                alert(error);
            });
        },
        deleteSiteCronJob: ({dispatch}, data) => {
            Vue.http.delete(Vue.action('Site\SiteCronJobController@destroy', {
                site: data.site,
                cron_job: data.cron_job
            })).then(() => {
                dispatch('getSiteCronJobs', data.site);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_SITE_CRON_JOBS: (state, site_cron_jobs) => {
            state.site_cron_jobs = site_cron_jobs;
        }
    }
}