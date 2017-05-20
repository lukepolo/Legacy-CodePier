export const get = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteEnvironmentVariablesController@index', { site: site }),
        'user_site_environment_variables/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteEnvironmentVariablesController@store', { site: data.site }),
        'user_site_environment_variables/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteEnvironmentVariablesController@destroy', {
            site: data.site,
            environment_variable: data.environment_variable
        }),
        'user_site_environment_variables/remove'
    )
}
