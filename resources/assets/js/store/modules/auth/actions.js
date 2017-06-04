export const logout = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Auth\LoginController@logout')
    ).then(() => {
        window.location = '/'
    })
}
