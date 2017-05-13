export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\UserSshKeyController@index'),
        'user_ssh_keys/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\UserSshKeyController@store'),
        'user_ssh_keys/add'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\UserSshKeyController@destroy', { ssh_key: data.ssh_key }),
        'user_ssh_keys/remove'
    )
}