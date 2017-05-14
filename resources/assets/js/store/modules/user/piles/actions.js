export const get = ({}, data) => {
    // TODO - why not get all the piles?
  // Vue.action('Pile\PileController@allPiles', 'setAll')
    return Vue.request(data).get(
        Vue.action('Pile\PileController@index'),
        'user_piles/setAll'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Pile\PileController@store'),
        'user_piles/add'
    )
}

export const update = ({}, data) => {
    return Vue.request(data).patch(
        Vue.action('Pile\PileController@update', { pile: data.pile }),
        'user_piles/update'
    )
}

export const destroy = ({}, pile) => {
    return Vue.request({
        pile : pile
    }).delete(
        Vue.action('Pile\PileController@destroy', { pile: pile }),
        'user_piles/remove'
    )
}

export const changePile = ({dispatch}, pile) => {
    Vue.request({
        pile : pile
    }).post('Pile\PileController@changePile', 'SET_USER').then(() => {

        dispatch('user_sites/get')
        dispatch('servers/get')

        if (app.$router.currentRoute.params.server_id || app.$router.currentRoute.params.site_id) {
            app.$router.push('/')
        }
    })
}

export const sites = ({}, pile) => {
    Vue.request().get(
        Vue.action('Pile\PileSitesController@index', { pile: pile }),
        'user_piles/setPileSites'
    )
}
