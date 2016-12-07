export default {
    state: {
        site_ssh_keys: [],
    },
    actions: {
        getSiteSshKeys: ({commit}, site_id) => {
            Vue.http.get(Vue.action('Site\SiteSshKeyController@index', {site: site_id})).then((response) => {
                commit('SET_SITE_SSH_KEYS', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        createSiteSshKey: ({commit, dispatch}, data) => {
            Vue.http.post(Vue.action('Site\SiteSshKeyController@store', {site: data.site}), data).then((response) => {
                dispatch('getSiteSshKeys', data.site);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteSiteSshKey: ({commit, dispatch}, data) => {
            Vue.http.delete(Vue.action('Site\SiteSshKeyController@destroy', {
                site: data.site,
                ssh_key: data.ssh_key
            })).then((response) => {
                dispatch('getSiteSshKeys', data.site);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        SET_SITE_SSH_KEYS: (state, site_ssh_keys) => {
            state.site_ssh_keys = site_ssh_keys;
        }
    }
}