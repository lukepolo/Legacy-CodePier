import { PilesState } from "./stateInterface";

export default function() {
  return {
    REMOVE_TEMP_PILE(state: PilesState, indexOfPile) {
      state.piles.splice(indexOfPile, 1);
    },
  };
}
