export const set = (state, { response }) => {
  Vue.set(state.stats, response.server_id, response);
};
