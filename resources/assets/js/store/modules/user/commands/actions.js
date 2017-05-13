export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\UserController@getRunningCommands'),
        'user_commands/setAll'
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


