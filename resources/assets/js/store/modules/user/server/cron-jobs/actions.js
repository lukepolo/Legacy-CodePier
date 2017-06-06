export const get = (context, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerCronJobController@index', { server: server }),
        'user_server_cron_jobs/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerCronJobController@store', { server: data.server }),
        'user_server_cron_jobs/add'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Server\ServerCronJobController@destroy', {
            server: data.server,
            cron_job: data.cron_job
        }),
        'user_server_cron_jobs/remove'
    )
}
