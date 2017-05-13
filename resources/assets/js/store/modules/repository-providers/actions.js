export const get = ({}, data) => {
    return Vue.request(data).get(
        Vue.http.get(Vue.action('Auth\Providers\RepositoryProvidersController@index'),
        'repository_providers/setAll'
        )
    )
}