import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { UserState } from "./stateInterface";

export default function($http) {
  return {
    sampleAction: (context: ActionContext<UserState, RootState>, data) => {
      return $http.post("/some-url", {
        data,
      });
    },
  };
}
