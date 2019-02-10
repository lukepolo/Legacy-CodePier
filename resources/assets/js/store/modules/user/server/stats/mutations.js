export const set = (state, { response }) => {
  Vue.set(state.stats, response.server_id, response);
};

export const update = (state, data) => {
  Vue.set(state.stats, data.server_id, data);
  state.stats = Object.assign({}, state.stats);
};
