import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { TeamsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<TeamsState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
