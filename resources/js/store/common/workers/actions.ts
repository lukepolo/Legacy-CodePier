import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { WorkersState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<WorkersState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
