export const now = () => {
    return moment()
}

export const timeAgo = (time) => {
    time = moment(time)

    const currentTime = moment()

    if (currentTime.diff(time, 'hour') < 5) {
        return time.fromNow()
    }

    return time.format('M-D-YY h:mm A')
}

export const parseDate = (date) => {
    return moment(date)
}

export const diff = (date1, date2) => {
    return moment(date2).preciseDiff(moment(date1))
}
