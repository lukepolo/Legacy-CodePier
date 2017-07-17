export const set = (state, { response }) => {
  Vue.set(state, "user", response);
};
