export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\UserController@getRunningDeployments'),
        'user_sites_deployments/setAll'
    )
}

export const show = ({}, data) => {
    return Vue.request(data).get('')
}

export const store = ({}, data) => {
    return Vue.request(data).post('')
}

export const update = ({}, data) => {
    return Vue.request(data).patch('')
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete('')
}