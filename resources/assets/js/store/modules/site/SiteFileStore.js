export default {
  state: {
    site_files: [],
    site_editable_files: [],
    editable_framework_files : []
  },
  actions: {
    getSiteFiles: ({ commit }, site) => {
      Vue.http.get(Vue.action('Site\SiteFileController@index', { site: site })).then((response) => {
        commit('SET_SITE_FILES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    addCustomFile: ({ commit }, site) => {
      Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFiles', { site: site })).then((response) => {
        commit('ADD_SITE_FILE', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getEditableFiles: ({ commit }, site) => {
      Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFiles', { site: site })).then((response) => {
        commit('SET_EDITABLE_SITE_FILES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    findSiteFile: ({ commit }, data) => {
      return Vue.http.post(Vue.action('Site\SiteFileController@find', {
        site: data.site
      }), {
        file: data.file,
        custom: data.custom ? data.custom : false
      }).then((response) => {
        commit('ADD_SITE_FILE', response.data)
        return response.data
      }, (errors) => {
        app.showError(errors)
      })
    },
    updateSiteFile: ({ commit }, data) => {
      Vue.http.put(Vue.action('Site\SiteFileController@update', {
        site: data.site,
        file: data.file_id
      }), {
        file_path: data.file,
        content: data.content
      }).then((response) => {

      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    reloadSiteFile: ({ commit }, data) => {
      console.info(data)
      Vue.http.post(Vue.action('Site\SiteFileController@reloadFile', {
        site: data.site,
        file: data.file,
        server: data.server
      })).then((response) => {
        commit('UPDATE_SITE_FILE', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getEditableFrameworkFiles: ({ commit }, site) => {
      Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFrameworkFiles', { site: site })).then((response) => {
        commit('SET_EDITABLE_FRAMEWORK_FILES', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    SET_SITE_FILES: (state, files) => {
      state.site_files = files
    },
    ADD_SITE_FILE: (state, file) => {
      state.site_files.push(file)
    },
    UPDATE_SITE_FILE: (state, file) => {
      Vue.set(state.site_files[_.findKey(state.site_files, { id: file.id })], 'unencrypted_content', file.unencrypted_content)
    },
    SET_EDITABLE_SITE_FILES: (state, files) => {
      state.site_editable_files = files
    },
    SET_EDITABLE_FRAMEWORK_FILES: (state, files) => {
        state.editable_framework_files = files
    },
  }
}
