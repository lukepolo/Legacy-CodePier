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

export const parseDate = (date, timezone) => {
    if (timezone) {
        return moment(date).tz(timezone)
    }
    return moment(date)
}

export const dateHumanize = (date, timezone) => {
    return moment(date).tz(timezone).fromNow()
}

