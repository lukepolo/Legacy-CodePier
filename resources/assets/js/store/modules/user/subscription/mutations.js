export const set = (state, { response }) => {
    state.subscription = response
}

export const remove = (state) => {
    state.subscription = null
}

export const setInvoices = (state, { response }) => {
    state.invoices = response
}

export const setUpcoming = (state, { response }) => {
    state.upcoming_subscription = response
}
