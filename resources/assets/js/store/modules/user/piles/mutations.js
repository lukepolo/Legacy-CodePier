export const setAll = (state, { response }) => {
    state.piles = response;
};

export const add = (state, { response }) => {
    state.piles.push(response);
};

export const update = (state, { response }) => {
    Vue.set(
        state.piles,
        parseInt(
            _.findKey(state.piles, {
                id: response.id
            })
        ),
        response
    );
};

export const remove = (state, { requestData }) => {
    Vue.set(
        state,
        "piles",
        _.reject(state.piles, {
            id: requestData.value
        })
    );
};

export const removeTemp = (state, index) => {
    state.piles.splice(index, 1);
};
