import { PilesState } from "./stateInterface";

export default class Mutations {
  SET_PILES = (state: PilesState, piles: []) => {
    state.piles = piles;
  };

  REMOVE_TEMP_PILE = (state: PilesState, indexOfPile) => {
    console.info(indexOfPile);
    console.info(state.piles);
    state.piles.splice(indexOfPile, 1);
  };
}
