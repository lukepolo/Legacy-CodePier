export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteWorkerController@index', { site: site }),
        'user_site_workers/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteWorkerController@store', { site: data.site }),
        'user_site_workers/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteWorkerController@destroy', {
            site: data.site,
            worker: data.worker
        }),
        'user_site_workers/remove'
    )
}