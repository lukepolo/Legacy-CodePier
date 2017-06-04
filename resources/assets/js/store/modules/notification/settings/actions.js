export const get = (context, data) => {
    return Vue.request(data).get(
        Vue.action('NotificationSettingsController@index'),
        'notification_settings/setAll'
    )
}
