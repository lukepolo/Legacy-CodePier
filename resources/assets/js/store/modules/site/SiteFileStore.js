export default {
    state: {
        site_files : [],
        site_editable_files :[],
    },
    actions: {
        getSiteFiles : ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteFileController@index', {site: site})).then((response) => {
                commit('SET_SITE_FILES', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        addCustomFile : ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFiles', {site: site})).then((response) => {
                commit('ADD_SITE_FILE', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        getEditableFiles: ({commit}, site) => {
            Vue.http.get(Vue.action('Site\SiteFeatureController@getEditableFiles', {site: site})).then((response) => {
                commit('SET_EDITABLE_SITE_FILES', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        findSiteFile : ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteFileController@find', {
                site: data.site,
            }), {
                file: data.file,
                custom : data.custom ? data.custom : false
            }).then((response) => {
                commit('ADD_SITE_FILE', response.data)
            }, (errors) => {
                app.showError(errors);
            });
        },
        updateSiteFile: ({commit}, data) => {
            Vue.http.put(Vue.action('Site\SiteFileController@update', {
                site: data.site,
                file: data.file_id
            }), {
                file_path: data.file,
                content: data.content,
                servers: data.servers,
            }).then((response) => {

            }, (errors) => {
                app.handleApiError(errors);
            });
        },
    },
    mutations: {
        SET_SITE_FILES: (state, files) => {
            state.site_files = files;
        },
        ADD_SITE_FILE: (state, file) => {
            state.site_files.push(file);
        },
        SET_EDITABLE_SITE_FILES : (state, files) => {
            state.site_editable_files = files;
        },
    }
}