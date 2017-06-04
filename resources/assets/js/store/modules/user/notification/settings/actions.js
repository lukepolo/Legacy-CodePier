export const get = () => {
    return Vue.request().get(
        Vue.action('User\UserNotificationSettingsController@index'),
        'user_notification_settings/setAll'
    )
}

export const update = (context, data) => {
    return Vue.request(data).post(
        Vue.action('User\UserNotificationSettingsController@store'),
        'user_notification_settings/setAll'
    )
}
