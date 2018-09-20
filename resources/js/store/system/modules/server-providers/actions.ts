import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ServerProvidersState } from "./stateInterface";

export default function($http) {
  return {
    sampleAction: (
      context: ActionContext<ServerProvidersState, RootState>,
      data,
    ) => {
      return $http.post("/some-url", {
        data,
      });
    },
  };
}
