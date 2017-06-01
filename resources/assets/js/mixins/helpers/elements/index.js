export const hasClass = function (target, classes) {
    let matchFound = false
    _.filter(classes, (className) => {
        if (className.indexOf('*') !== -1) {
            if (target.className.indexOf(className.replace('*', '')) !== -1) {
                matchFound = true
                return false
            }
        }
    })

    if (matchFound) {
        return true
    }

    return _.intersection(target.className.split(' '), classes).length > 0
}

export const isTag = function (target, tag) {
    return target.tagName.toLowerCase() === tag.toLowerCase()
}
