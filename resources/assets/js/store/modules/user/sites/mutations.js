export const set = (state, { response }) => {
    state.site = response
}

export const setAll = (state, { response }) => {
    state.sites = response
}

export const add = (state, { response }) => {
    state.sites.push(response)
}

export const update = (state, { response }) => {
    Vue.set(state.sites,
        parseInt(_.findKey(state.sites, { id: response.id })),
        response
    )
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'sites', _.reject(state.sites, { id: requestData.value }))
}

export const listenTo = (state, site) => {
    state.listening_to.push(site)
}
