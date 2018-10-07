import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { FirewallRulesState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<FirewallRulesState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
