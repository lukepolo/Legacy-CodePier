export const get = ({}, data) => {
    return Vue.request(data).get(
        Vue.action('Auth\Providers\NotificationProvidersController@index'),
        'notification_providers/setAll'
    )
}