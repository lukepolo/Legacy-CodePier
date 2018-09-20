import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SystemState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<SystemState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
