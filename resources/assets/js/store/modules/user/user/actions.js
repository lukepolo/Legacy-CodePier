export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\UserController@index'),
        'user/set'
    )
}

export const update = ({}, data) => {
    return Vue.request(data).patch(
        Vue.action('User\UserController@update', { user : data.user }),
        'user/set'
    )
}
