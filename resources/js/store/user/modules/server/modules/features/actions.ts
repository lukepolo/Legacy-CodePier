import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { FeaturesState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<FeaturesState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
