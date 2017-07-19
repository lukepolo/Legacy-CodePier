export const set = (state, { response }) => {
    Vue.set(state, 'buoys', response);
};

export const remove = (state, { requestData }) => {
    Vue.set(state, 'buoys', _.reject(state.buoys, { id: requestData.buoy }));
};

export const all = (state, { response }) => {
    Vue.set(state, 'all', response);
};
