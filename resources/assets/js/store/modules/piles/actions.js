export const get = ({}, data) => {
    // TODO - why not get all the piles?
  // Vue.action('Pile\PileController@allPiles', 'setAll')
    return Vue.request(data).get(
        Vue.action('Pile\PileController@index'),
        'setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Pile\PileController@store'),
        'add'
    )
}

export const update = ({}, data) => {
    return Vue.request(data).patch(
        Vue.action('Pile\PileController@update', { pile: data.pile }),
        'update'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('Pile\PileController@destroy', { pile: data.pile }),
        'destroy'
    )
}

export const changePile = ({dispatch}, pile) => {
    Vue.request({
        pile : pile
    }).post('Pile\PileController@changePile', 'SET_USER').then(() => {

        dispatch('getSites')
        dispatch('getServers')

        if (app.$router.currentRoute.params.server_id || app.$router.currentRoute.params.site_id) {
            app.$router.push('/')
        }
    })
}

export const sites = ({}, pile) => {
    Vue.request().get(
        Vue.action('Pile\PileSitesController@index', { pile: pile }),
        'setPileSites'
    )
}
