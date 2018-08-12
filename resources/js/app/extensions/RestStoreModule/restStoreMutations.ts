export default function(stateName) {
  return {
    [`SET_${stateName.toUpperCase()}`]: (state, data) => {
      state[stateName] = data;
    },
  };
}
