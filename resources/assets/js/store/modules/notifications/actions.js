export const get = ({}) => {

}

export const add = ({}, notification) => {
    commit('notifications/add', notification)
}

export const remove = ({}, notification) => {
    commit('notifications/remove', notification)
}