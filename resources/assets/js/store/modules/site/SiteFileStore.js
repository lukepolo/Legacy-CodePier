export default {
    state: {

    },
    actions: {
        saveSiteFile: ({commit}, data) => {
            Vue.http.post(Vue.action('Site\SiteFileController@store', {
                site: data.site
            }), {
                file_path: data.file,
                content: data.content,
                servers: data.servers,
            }).then((response) => {

            }, (errors) => {
                alert(error);
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
                alert(error);
            });
        }
    },
    mutations: {

    }
}