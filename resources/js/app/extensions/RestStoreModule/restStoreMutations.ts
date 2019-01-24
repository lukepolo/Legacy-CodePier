import pluralize from "pluralize";

export default function(stateName) {
  let pluralized = pluralize(stateName).toLowerCase();
  let singular = pluralize.singular(stateName).toLowerCase();

  function getItemKeyById(state, stateName, data) {
    let tempData = state[pluralized];
    if (typeof tempData.map === "function") {
      return tempData.map((datum) => datum.id).indexOf(data);
    }
    return false;
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

      if (key === false) {
        Object.assign({}, data);
      }

      if (key !== false && key > -1) {
        Object.assign(state[pluralized][key], state[pluralized][key], data);
      }

      if (
        state[singular] &&
        state[singular].hasOwnProperty("id") &&
        data.hasOwnProperty("id") &&
        state[singular].id === data.id
      ) {
        state[singular] = data;
      }
    },
    [`DESTROYED_${singular.toUpperCase()}`]: (state, data) => {
      let key = getItemKeyById(state, singular, data[singular]);
      if (key > -1) {
        state[pluralized].splice(key, 1);
      }
    },
  };
}
