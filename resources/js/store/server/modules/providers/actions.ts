import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ServerProvidersState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<ServerProvidersState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
