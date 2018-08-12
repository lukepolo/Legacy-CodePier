export default function(stateName) {
  return {
    SET_PILES(state, data) {
      console.info(state, stateName, data);
      state[stateName] = data;
    },
  };
}
