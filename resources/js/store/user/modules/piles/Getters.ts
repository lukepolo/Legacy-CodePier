import PileModel from "@models/PileModel";
import { PilesState } from "./stateInterface";

export default class Getters {
  pileById = (state: PilesState) => (pileId) => {
    return state.piles[
      state.piles.map((pile: PileModel) => pile.id).indexOf(pileId)
    ];
  };
}
