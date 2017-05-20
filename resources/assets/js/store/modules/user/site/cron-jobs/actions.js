export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteCronJobController@index', { site: site }),
        'user_site_cron_jobs/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteCronJobController@store', { site: data.site }),
        'user_site_cron_jobs/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteCronJobController@destroy', {
            site: data.site,
            cron_job: data.cron_job
        }),
        'user_site_cron_jobs/remove'
    )
}
