export const get = () => {
    return Vue.request().get(
        Vue.action('User\UserSshKeyController@index'),
        'user_ssh_keys/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('User\UserSshKeyController@store'),
        'user_ssh_keys/add'
    )
}

export const destroy = (context, ssh_key) => {
    return Vue.request(ssh_key).delete(
        Vue.action('User\UserSshKeyController@destroy', { ssh_key: ssh_key }),
        'user_ssh_keys/remove'
    )
}
