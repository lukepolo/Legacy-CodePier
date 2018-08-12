import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SitesState } from "./stateInterface";

export default function($http) {
  return {
    changePile: (context: ActionContext<SitesState, RootState>, data) => {
      return $http.post("/some-url", {
        data,
      });
    },
  };
}
