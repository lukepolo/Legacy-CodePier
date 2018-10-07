import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { EnvironmentVariablesState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<EnvironmentVariablesState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
