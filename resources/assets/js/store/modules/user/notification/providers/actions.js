export const get = (context, user) => {
    return Vue.request().get(
        Vue.action('UserProvidersUserNotificationProviderController@index', {
            user: user,
        }),
        'user_notification_providers/setAll',
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('UserProvidersUserNotificationProviderController@destroy', {
            user: data.user,
            notification_provider: data.notification_provider,
        }),
        'user_notification_providers/destroy',
    );
};
