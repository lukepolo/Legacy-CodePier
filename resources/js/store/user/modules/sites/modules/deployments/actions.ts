import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { DeploymentsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<DeploymentsState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
