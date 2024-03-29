export const setAll = (state, { response }) => {
  state.files = response;
};

export const add = (state, { response }) => {
  state.files.push(response);
};

export const update = (state, { response }) => {
  Vue.set(state.files, _.findKey(state.files, { id: response.id }), response);
};

export const remove = (state, { requestData }) => {
  Vue.set(state, "files", _.reject(state.files, { id: requestData.file }));
};
