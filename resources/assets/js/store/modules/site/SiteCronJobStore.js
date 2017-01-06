export default {
  state: {
    site_cron_jobs: []
  },
  actions: {
    getSiteCronJobs: ({ commit }, site) => {
      Vue.http.get(Vue.action('Site\SiteCronJobController@index', { site: site })).then((response) => {
        commit('SET_SITE_CRON_JOBS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    createSiteCronJob: ({ commit }, data) => {
      Vue.http.post(Vue.action('Site\SiteCronJobController@store', { site: data.site }), data).then((response) => {
        commit('ADD_SITE_CRON_JOB', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    deleteSiteCronJob: ({ commit }, data) => {
      Vue.http.delete(Vue.action('Site\SiteCronJobController@destroy', {
        site: data.site,
        cron_job: data.cron_job
      })).then(() => {
        commit('REMOVE_SITE_CRON_JOB', data.cron_job)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    ADD_SITE_CRON_JOB: (state, cronJob) => {
      state.site_cron_jobs.push(cronJob)
    },
    REMOVE_SITE_CRON_JOB: (state, cronJob) => {
      Vue.set(state, 'site_cron_jobs', _.reject(state.site_cron_jobs, { id: cronJob }))
    },
    SET_SITE_CRON_JOBS: (state, siteCronJobs) => {
      state.site_cron_jobs = siteCronJobs
    }
  }
}
