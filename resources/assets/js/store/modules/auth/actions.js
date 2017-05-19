export const logout = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Auth\LoginController@logout')
    ).then(() => {
        window.location = '/'
    })
}
