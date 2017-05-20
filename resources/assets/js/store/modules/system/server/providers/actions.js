export const get = ({}) => {
    return Vue.request().get(
        Vue.action('Auth\Providers\ServerProvidersController@index'),
        'server_providers/setAll'
    )
}

export const getFeatures = ({}, provider) => {
    return Vue.request().get(
        '/api/server/providers/' + provider + '/features',
        'server_providers/setFeatures'
    )
}

export const getOptions = ({}, provider) => {
    return Vue.request().get(
        '/api/server/providers/' + provider + '/options',
        'server_providers/setOptions'
    )
}

export const getRegions = ({}, provider) => {
    return Vue.request().get(
        '/api/server/providers/' + provider + '/regions',
        'server_providers/setRegions'
    )
}

