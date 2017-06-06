export const get = () => {
    return Vue.request().get(
        Vue.action('Pile\PileController@index'),
        'user_piles/setAll'
    )
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Pile\PileController@store'),
        'user_piles/add'
    )
}

export const update = (context, data) => {
    return Vue.request(data).patch(
        Vue.action('Pile\PileController@update', { pile: data.pile }),
        'user_piles/update'
    )
}

export const destroy = (context, pile) => {
    return Vue.request(pile).delete(
        Vue.action('Pile\PileController@destroy', { pile: pile }),
        'user_piles/remove'
    )
}

export const change = (context, pile) => {
    return Vue.request({
        pile: pile
    }).post(
        Vue.action('Pile\PileController@changePile'),
        'user/set'
    ).then(() => {
        if (app.$router.currentRoute.params.server_id || app.$router.currentRoute.params.site_id) {
            app.$router.push('/')
        }
    })
}

export const sites = (context, pile) => {
    return Vue.request().get(
        Vue.action('Pile\PileSitesController@index', { pile: pile }),
        'user_piles/setPileSites'
    )
}
