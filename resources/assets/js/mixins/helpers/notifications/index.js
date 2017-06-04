export const showError = function (message, title, timeout) {
    if (timeout === undefined) {
        timeout = 5000
    }

    this.$store.dispatch('notifications/add', {
        title: !_.isEmpty(title) ? title : 'Error!!',
        text: message,
        class: 'error',
        timeout: timeout
    })
}
export const showSuccess = function (message, title, timeout) {
    if (timeout === undefined) {
        timeout = 5000
    }

    this.$store.dispatch('notifications/add', {
        title: !_.isEmpty(title) ? title : 'Success!!',
        text: message,
        class: 'success',
        timeout: timeout
    })
}

export const handleApiError = function (response) {
    let message = response

    if (_.isObject(response)) {
        if (_.isSet(response.errors)) {
            message = response.errors
        } else if (_.isObject(response.data)) {
            message = ''
            _.each(response.data, function (error) {
                message += error + '<br>'
            })
        } else {
            message = response.data
        }
    }

    if (_.isString(message)) {
        this.showError(message)
    } else {
        console.warn('UNABLE TO PARSE ERROR')
    }
}
