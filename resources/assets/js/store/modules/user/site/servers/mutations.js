export const set = (state, { response }) => {

}

export const setAll = (state, { response, requestData }) => {
    Vue.set(state.servers, requestData.value, response)
}

export const add = (state, { response, requestData }) => {

}

export const update = (state, { response, requestData }) => {

}

export const remove = (state, { requestData }) => {
    console.info(requestData)
    Vue.set(state, 'servers', _.reject(state.servers, { id: requestData.value }))

}
