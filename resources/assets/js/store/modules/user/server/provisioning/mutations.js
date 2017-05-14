export const set = (state, {response, requestData}) => {

}

export const setAll = (state, {response, requestData}) => {

}

export const add = (state, {response, requestData}) => {

}

export const update = (state, {response, requestData}) => {

}

export const remove = (state, {response, requestData}) => {

}

export const setCurrentStep = (state, {response, requestData}) => {
    Vue.set(state.current_step, requestData.server, response)
}