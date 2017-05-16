export const logout = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Auth\LoginController@logout')
    ).then(() => {
        console.info('no error')
        window.location = '/'
    }, (errors) => {
        console.info('errors')
        window.location = '/'
    })
}
