export const get = ({}) => {
    return Vue.request().get(
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
    return Vue.request(pile).delete(
        Vue.action('Pile\PileController@destroy', { pile: pile }),
        'user_piles/remove'
    )
}

export const change = ({}, pile) => {
    Vue.request({
        pile: pile
    }).post(
        Vue.action('Pile\PileController@changePile'),
        'user/set',
    ).then(() => {
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
