export default function(stateName) {
  function getItemKeyById(state, stateName, data) {
    let tempData = state[`${stateName}s`];
    return tempData.map((datum) => datum.id).indexOf(data);
  }

  return {
    [`SET_${stateName.toUpperCase()}S`]: (state, data) => {
      state[`${stateName}s`] = data;
    },
    [`SHOW_${stateName.toUpperCase()}`]: (state, data) => {
      alert("not ready yet");
    },
    [`CREATED_${stateName.toUpperCase()}`]: (state, data) => {
      state[`${stateName}s`].push(data);
    },
    [`UPDATED_${stateName.toUpperCase()}`]: (state, data) => {
      let key = getItemKeyById(state, stateName, data.id);
      if (key) {
        Object.assign(
          state[`${stateName}s`][key],
          state[`${stateName}s`][key],
          data,
        );
      }
    },
    [`DESTROYED_${stateName.toUpperCase()}`]: (state, data) => {
      let key = getItemKeyById(state, stateName, data[stateName]);
      if (key > -1) {
        state[`${stateName}s`].splice(key, 1);
      }
    },
  };
}
