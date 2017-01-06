export default {
  state: {
    workers: []
  },
  actions: {
    getWorkers: ({ commit }, site_id) => {
      Vue.http.get(Vue.action('Site\SiteWorkerController@show', { site: site_id })).then((response) => {
        commit('SET_SITE_WORKERS', response.data)
      }, (errors) => {
      })
    },
    installWorker: ({ commit }, data) => {
      Vue.http.post(Vue.action('Site\SiteWorkerController@store', { site: data.site_id }), data).then((response) => {
        commit('ADD_SITE_WORKER', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    deleteWorker: ({ commit }, data) => {
      Vue.http.delete(Vue.action('Site\SiteWorkerController@destroy', {
        site: data.site,
        worker: data.worker
      })).then(() => {
        commit('REMOVE_SITE_WORKER', data.worker)
      }, (errors) => {
      })
    }
  },
  mutations: {
    ADD_SITE_WORKER: (state, worker) => {
      state.workers.push(worker)
    },
    REMOVE_SITE_WORKER: (state, worker_id) => {
      Vue.set(state, 'workers', _.reject(state.workers, { id: worker_id }))
    },
    SET_SITE_WORKERS: (state, workers) => {
      state.workers = workers
    }
  }
}
