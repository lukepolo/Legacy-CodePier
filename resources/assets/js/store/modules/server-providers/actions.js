export const get = ({}, data) => {
    return Vue.request(data).get('')
}