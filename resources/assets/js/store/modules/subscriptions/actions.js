export const plans = ({}, data) => {
    return Vue.request(data).get(
        Vue.action('SubscriptionController@index'),
        'subscriptions/setAll'
    )
}

