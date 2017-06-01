export const get = (context, data) => {
    let filters = (data && data.filters) ? data.filters : null

    filters = _.merge({
        page: data ? data.page : 1
    },
        _.omitBy({
            types: filters ? _.omitBy(filters.types, _.isEmpty) : null,
            piles: filters ? filters.piles : null,
            sites: filters ? filters.sites : null,
            servers: filters ? filters.servers : null
        }, _.isEmpty)
    )

    return Vue.request(filters).post(
        Vue.action('EventController@store'),
        'events/setAll'
    )
}
