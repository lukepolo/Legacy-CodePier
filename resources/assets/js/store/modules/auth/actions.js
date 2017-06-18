export const logout = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Auth\LoginController@logout')
    ).then(() => {
        window.location = '/'
    })
}

export const getSecondAuthQr = () => {
    return Vue.request().get(
        Vue.action('Auth\SecondAuthController@index')
    )
}

export const validateSecondAuth = (context, token) => {
    return Vue.request({
        token : token
    }).post(
        Vue.action('Auth\SecondAuthController@store'),
        'user/set'
    )
}
