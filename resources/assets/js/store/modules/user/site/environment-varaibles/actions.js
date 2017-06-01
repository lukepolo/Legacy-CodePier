export const get = (context, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteEnvironmentVariablesController@index', { site: site }),
        'user_site_environment_variables/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteEnvironmentVariablesController@store', { site: data.site }),
        'user_site_environment_variables/add'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteEnvironmentVariablesController@destroy', {
            site: data.site,
            environment_variable: data.environment_variable
        }),
        'user_site_environment_variables/remove'
    )
}
