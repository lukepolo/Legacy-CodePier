import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { BittsState } from "./stateInterface";
import BittService from "../../app/services/BittService";

export default function(bittService: BittService) {
  return {
    sampleAction: (context: ActionContext<BittsState, RootState>, data) => {},
  };
}
