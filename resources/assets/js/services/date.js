export function now() {
    return moment();
}

export function parseDate(date, timezone) {
    if (timezone) {
        return moment(date).tz(timezone);
    }
    return moment(date);
}

export function dateHumanize(date, timezone) {
    return moment(date).tz(timezone).fromNow();
}