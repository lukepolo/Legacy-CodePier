export const plans = (context, data) => {
    return Vue.request(data).get(
        Vue.action('SubscriptionController@index'),
        'subscriptions/setAll'
    )
}

