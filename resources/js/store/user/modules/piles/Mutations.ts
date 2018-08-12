import { PilesState } from "./stateInterface";

export default function() {
  return {
    REMOVE_TEMP_PILE(state: PilesState, indexOfPile) {
      console.info(indexOfPile);
      console.info(state.piles);
      state.piles.splice(indexOfPile, 1);
    },
  };
}
