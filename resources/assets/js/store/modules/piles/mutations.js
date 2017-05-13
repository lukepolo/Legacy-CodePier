export const setAll = (state, {response}) => {
    state.piles = response
}

export const add = (state, {response}) => {
    state.piles.push(response)
}

export const update = (state, {response}) => {
    Vue.set(state.piles,
        parseInt(
            _.findKey(state.piles, {
                id : pile.id
            })
        ),
        response
    )
}

export const remove = (state, {requestData}) => {
    Vue.set(state, 'piles', _.reject(state.piles, {
        id: requestData.pile
    }))
}

export const removeTemp = (state, index) => {
    state.piles.splice(index, 1)
}

export const setPileSites = (state, {response, requestData}) => {
    Vue.set(state.pile_sites, requestData.pile, response)
}

export const removeFromPileSites = (state, {requestData}) => {
    alert('remove site from pile')
    // Vue.set(state.pile_sites, requestData.pile, requestData.site)
}
