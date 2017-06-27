export const get = () => {
    return Vue.request().get(
        Vue.action("UserSubscriptionUserSubscriptionController@index"),
        "user_subscription/set"
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action("UserSubscriptionUserSubscriptionController@store"),
        "user_subscription/set"
    );
};

export const cancel = (context, data) => {
    return Vue.request(data).delete(
        Vue.action("UserSubscriptionUserSubscriptionController@destroy", {
            subscription: subscription
        }),
        "tAll/remove"
    );
};

export const getInvoices = () => {
    return Vue.request().get(
        Vue.action("UserSubscriptionUserSubscriptionInvoiceController@index"),
        "user_subscription/setUpcoming"
    );
};

export const getUpcomingSubscription = () => {
    return Vue.request().get(
        Vue.action(
            "UserSubscriptionUserSubscriptionUpcomingInvoiceController@index"
        ),
        "user_subscription/setUpcoming"
    );
};
