export const get = ({}, user) => {
    return Vue.request().get(
        Vue.action('User\Providers\UserServerProviderController@index', { user: user }),
        'setAll'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Providers\UserServerProviderController@destroy', {
            user: data.user,
            server_provider: data.server_provider
        }),
        'remove'
    )
}