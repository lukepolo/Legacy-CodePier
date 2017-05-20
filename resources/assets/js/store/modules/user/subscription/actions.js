export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\Subscription\UserSubscriptionController@index'),
        'user_subscription/set'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\Subscription\UserSubscriptionController@store'),
        'user_subscription/set'
    )
}

export const cancel = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Subscription\UserSubscriptionController@destroy', {
            subscription: subscription
        }),
        'tAll/remove'
    )
}

export const getInvoices = ({}) => {
    return Vue.request().get(
        Vue.action('User\Subscription\UserSubscriptionInvoiceController@index'),
        'user_subscription/setUpcoming'
    )
}

export const getUpcomingSubscription = ({}) => {
    return Vue.request().get(
        Vue.action('User\Subscription\UserSubscriptionUpcomingInvoiceController@index'),
        'user_subscription/setUpcoming'
    )
}
