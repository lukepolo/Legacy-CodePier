import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ServerState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<ServerState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
