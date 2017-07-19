export const now = () => {
    return moment();
};

export const parseDate = date => {
    return moment(date);
};

export const diff = (date1, date2) => {
    return moment(date2).preciseDiff(moment(date1));
};
