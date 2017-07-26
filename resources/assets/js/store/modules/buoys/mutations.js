export const set = (state, { response }) => {
  state.buoy_app = response;
};

export const setAll = (state, { response }) => {
  state.buoy_apps = response;
};

export const remove = (state, { requestData }) => {
  Vue.set(
    state,
    "buoy_apps",
    _.reject(state.buoy_app, { id: requestData.value })
  );
};
