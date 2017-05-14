export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteFileController@index', { site: site }),
        'user_site_files/setAll'
    )
}

export const show = ({}, data) => {
}

export const store = ({}, data) => {
    return Vue.request(data).post('')
}

export const update = ({}, data) => {
    return Vue.request(data).patch('')
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete('')
}
//
// getSiteFiles: ({ commit }, site) => {
//     Vue.http.get().then((response) => {
//         commit('SET_SITE_FILES', response.data)
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     addCustomFile: ({ commit }, site) => {
//     Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFiles', { site: site })).then((response) => {
//         commit('ADD_SITE_FILE', response.data)
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     getEditableFiles: ({ commit }, site) => {
//     Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFiles', { site: site })).then((response) => {
//         commit('SET_EDITABLE_SITE_FILES', response.data)
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     findSiteFile: ({ commit }, data) => {
//     return Vue.http.post(Vue.action('Site\SiteFileController@find', {
//         site: data.site
//     }), {
//         file: data.file,
//         custom: data.custom ? data.custom : false
//     }).then((response) => {
//         commit('ADD_SITE_FILE', response.data)
//         return response.data
//     }, (errors) => {
//         app.showError(errors)
//     })
// },
//     updateSiteFile: ({ commit }, data) => {
//     Vue.http.put(Vue.action('Site\SiteFileController@update', {
//         site: data.site,
//         file: data.file_id
//     }), {
//         file_path: data.file,
//         content: data.content
//     }).then((response) => {
//         app.showSuccess('You have updated the file')
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     reloadSiteFile: ({ commit }, data) => {
//     Vue.http.post(Vue.action('Site\SiteFileController@reloadFile', {
//         site: data.site,
//         file: data.file,
//         server: data.server
//     })).then((response) => {
//         commit('UPDATE_SITE_FILE', response.data)
//         app.showSuccess('You have reloaded the file')
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     getEditableFrameworkFiles: ({ commit }, site) => {
//     Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFrameworkFiles', { site: site })).then((response) => {
//         commit('SET_EDITABLE_FRAMEWORK_FILES', response.data)
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// }