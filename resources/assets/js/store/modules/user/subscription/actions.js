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

export const patch = (context, data) => {
    return Vue.request(data).patch(
        Vue.action("UserSubscriptionUserSubscriptionController@update", {
            subscription : data.subscription
        }),
        "user_subscription/set"
    );
};

export const cancel = (context, subscription) => {
    return Vue.request().delete(
        Vue.action("UserSubscriptionUserSubscriptionController@destroy", {
            subscription: subscription
        }),
        "user_subscription/set"
    );
};

export const getInvoices = () => {
    return Vue.request().get(
        Vue.action("UserSubscriptionUserSubscriptionInvoiceController@index"),
        "user_subscription/setInvoices"
    );
};

export const updateCard = (context, data) => {
    return Vue.request(data).post(
        Vue.action("UserSubscriptionUserSubscriptionCardController@store"),
        "user_subscription/set"
    );
}