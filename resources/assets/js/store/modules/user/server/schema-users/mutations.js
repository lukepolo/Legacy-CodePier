export const setAll = (state, { response }) => {
  state.users = response;
};

export const add = (state, { response }) => {
  state.users.push(response);
};

export const remove = (state, { requestData }) => {
  Vue.set(
    state,
    'users',
    _.reject(state.users, { id: requestData.schema_user }),
  );
};
