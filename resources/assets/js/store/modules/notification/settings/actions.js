export const get = ({}, data) => {
    return Vue.request(data).get(
        Vue.action('NotificationSettingsController@index'),
        'notification_settings/setAll'
    )
}
