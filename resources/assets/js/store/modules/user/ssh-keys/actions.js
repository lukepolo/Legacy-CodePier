export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\UserSshKeyController@index'),
        'setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\UserSshKeyController@store'),
        'add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\UserSshKeyController@destroy', { ssh_key: data.ssh_key }),
        'remove'
    )
}