export const setAll = (state, { response }) => {
  state.daemons = response;
};

export const add = (state, { response }) => {
  state.daemons.push(response);
};

export const update = (state, { response }) => {
  Vue.set(
    state.daemons,
    parseInt(_.findKey(state.daemons, { id: response.id })),
    response
  );
};

export const remove = (state, { requestData }) => {
  Vue.set(
    state,
    "daemons",
    _.reject(state.daemons, { id: requestData.cron_job })
  );
};
