import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { LifeLinesState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<LifeLinesState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
