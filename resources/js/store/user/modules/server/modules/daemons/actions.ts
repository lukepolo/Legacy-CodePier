import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { DaemonsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<DaemonsState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
