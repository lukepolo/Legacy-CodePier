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
        createSiteSshKey: ({commit,}, data) => {
            Vue.http.post(Vue.action('Site\SiteSshKeyController@store', {site: data.site}), data).then((response) => {
                commit('ADD_SITE_SSH_KEY', response.data);
            }, (errors) => {
                app.showError(errors);
            });
        },
        deleteSiteSshKey: ({commit}, data) => {
            Vue.http.delete(Vue.action('Site\SiteSshKeyController@destroy', {
                site: data.site,
                ssh_key: data.ssh_key
            })).then((response) => {
                commit('REMOVE_SITE_SSH_KEY', data.ssh_key);
            }, (errors) => {
                app.showError(errors);
            });
        }
    },
    mutations: {
        ADD_SITE_SSH_KEY: (state, ssh_key) => {
            state.site_ssh_keys.push(ssh_key);
        },
        REMOVE_SITE_SSH_KEY : (state, ssh_key_id) => {
            Vue.set(state, 'site_ssh_keys', _.reject(state.site_ssh_keys, { id : ssh_key_id }));
        },
        SET_SITE_SSH_KEYS: (state, site_ssh_keys) => {
            state.site_ssh_keys = site_ssh_keys;
        }
    }
}