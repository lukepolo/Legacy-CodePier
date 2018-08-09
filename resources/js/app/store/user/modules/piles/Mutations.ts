import { PilesState } from "./stateInterface";

export default class Mutations {
  SET_PILES = (state : PilesState, piles : []) => {
    state.piles = piles;
  };
}
