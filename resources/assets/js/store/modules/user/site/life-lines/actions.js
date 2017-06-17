export const get = ({ commit }, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteLifelinesController@index', { site: site }),
        'user_site_life_lines/setAll'
    ).then((lifelines) => {

        _.each(lifelines, (lifeline) => {
            Echo.private('App.Models.Site.Lifeline.' + lifeline.id)
                .listen('Site\\LifeLineUpdated', (data) => {
                    commit('update', {
                        response : data.lifeline
                    })
                })
        })

        return lifelines
    })
}

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteLifelinesController@store', { site: data.site }),
        'user_site_life_lines/add'
    )
}

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('Site\SiteLifelinesController@destroy', {
            site: data.site,
            life_line: data.life_line
        }),
        'user_site_life_lines/remove'
    )
}
