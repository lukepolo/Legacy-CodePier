export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteSshKeyController@index', { site: site }),
        'user_site_ssh_keys/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteSshKeyController@store', { site: data.site }),
        'user_site_ssh_keys/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteSshKeyController@destroy', {
            site: data.site,
            ssh_key: data.ssh_key
        }),
        'user_site_ssh_keys/remove'
    )
}
