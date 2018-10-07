import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SchmeasState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<SchmeasState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
