export const setCurrentStep = (state, { response }) => {
    Vue.set(state, 'current_step', response)
}
