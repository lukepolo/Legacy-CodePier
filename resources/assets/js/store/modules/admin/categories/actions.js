export const get = (context, data) => {
    return Vue.request(data).get('')
}

export const show = (context, data) => {
    return Vue.request(data).get('')
}

export const store = (context, data) => {
    return Vue.request(data).post('')
}

export const update = (context, data) => {
    return Vue.request(data).patch('')
}

export const destroy = (context, data) => {
    return Vue.request(data).delete('')
}
