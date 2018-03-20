export const setAll = (state, { response }) => {
  Vue.set(state, "bitts", response);
};

export const set = (state, bitt) => {
  Vue.set(state, "bitt", bitt);
};

export const add = (state, { response }) => {
  state.bitts.push(response);
};

export const update = (state, { response }) => {
  Vue.set(
    state.bitts,
    parseInt(
      _.findKey(state.bitts, {
        id: response.id,
      }),
    ),
    response,
  );
};
