import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SystemState } from "./stateInterface";

export default function($http) {
  return {
    sampleAction: (context: ActionContext<SystemState, RootState>, data) => {
      return $http.post("/some-url", {
        data,
      });
    },
  };
}
