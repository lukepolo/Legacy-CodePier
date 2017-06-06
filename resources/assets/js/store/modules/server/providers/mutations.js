export const setAll = (state, { response }) => {
    state.providers = response
}

export const setFeatures = (state, { response }) => {
    state.features = response
}

export const setOptions = (state, { response }) => {
    state.options = response
}

export const setRegions = (state, { response }) => {
    state.regions = response
}
