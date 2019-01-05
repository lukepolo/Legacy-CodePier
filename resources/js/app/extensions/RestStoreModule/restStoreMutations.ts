import pluralize from "pluralize";

export default function(stateName) {
  let pluralized = pluralize(stateName).toLowerCase();
  let singular = pluralize.singular(stateName).toLowerCase();

  function getItemKeyById(state, stateName, data) {
    let tempData = state[pluralized];
    return tempData.map((datum) => datum.id).indexOf(data);
  }

  return {
    [`SET_${pluralized.toUpperCase()}`]: (state, data) => {
      state[pluralized] = data;
    },
    [`SHOW_${singular.toUpperCase()}`]: (state, data) => {
      state[singular] = data;
    },
    [`CREATED_${singular.toUpperCase()}`]: (state, data) => {
      state[pluralized].push(data);
    },
    [`UPDATED_${singular.toUpperCase()}`]: (state, data) => {
      let key = getItemKeyById(state, singular, data.id);
      if (key > -1) {
        Object.assign(state[pluralized][key], state[pluralized][key], data);
      }
      if (state[singular] && state[singular].id === data.id) {
        state[singular] = data;
      }
    },
    [`DESTROYED_${singular.toUpperCase()}`]: (state, data) => {
      let key = getItemKeyById(state, singular, data[Object.keys(data)[0]]);
      if (key > -1) {
        state[pluralized].splice(key, 1);
      }
    },
  };
}
