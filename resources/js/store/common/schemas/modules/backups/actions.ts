import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { BackupsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<BackupsState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
