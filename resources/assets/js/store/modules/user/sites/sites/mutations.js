export const set = (state, {response}) => {
    state.site = response
}

export const setAll = (state, {response}) => {
    state.sites = response
}

export const add = (state, {response, requestData}) => {

}

export const update = (state, {response, requestData}) => {

}

export const remove = (state, {response, requestData}) => {

}

export const listenTo = (state, site) => {
    state.listening_to.push(site)
}