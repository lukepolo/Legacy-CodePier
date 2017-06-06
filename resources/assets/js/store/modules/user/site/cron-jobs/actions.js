export const get = (context, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteCronJobController@index', { site: site }),
        'user_site_cron_jobs/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteCronJobController@store', { site: data.site }),
        'user_site_cron_jobs/add'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteCronJobController@destroy', {
            site: data.site,
            cron_job: data.cron_job
        }),
        'user_site_cron_jobs/remove'
    )
}
