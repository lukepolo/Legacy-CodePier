export const get = ({}, user) => {
    return Vue.request().get(
        Vue.action('User\Providers\UserNotificationProviderController@index', { user: user }),
        'user_notification_providers/setAll'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Providers\UserNotificationProviderController@destroy', {
            user: data.user,
            notification_provider: data.notification_provider
        }),
        'user_notification_providers/destroy'
    )
}
