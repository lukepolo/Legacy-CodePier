export const setAll = (state, { response }) => {
    state.ssl_certificates = response
}

export const add = (state, { response }) => {
    state.ssl_certificates.push(response)
}

export const update = (state, { response }) => {
    Vue.set(state.ssl_certificates,
        parseInt(_.findKey(state.ssl_certificates, { id: response.id })),
        response
    )
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'ssl_certificates', _.reject(state.ssl_certificates, { id: requestData.ssl_certificate }))
}
