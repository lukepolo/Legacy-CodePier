export const setAll = (state, { response }) => {
    state.firewall_rules = response
}

export const add = (state, { response }) => {
    state.firewall_rules.push(response)
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'firewall_rules', _.reject(state.firewall_rules, { id: requestData.firewall_rule }))
}
