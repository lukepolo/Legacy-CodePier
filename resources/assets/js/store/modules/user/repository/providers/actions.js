export const get = ({}, user) => {
    return Vue.request().get(
        Vue.action('User\Providers\UserRepositoryProviderController@index', { user: user }),
        'user_repository_providers/setAll'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Providers\UserRepositoryProviderController@destroy', {
            user: data.user,
            repository_provider: data.repository_provider
        }),
        'user_repository_providers/remove'
    )
}
