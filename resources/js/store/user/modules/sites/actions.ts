import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SitesState } from "./stateInterface";

export default function(httpService) {
  return {
    changePile: (context: ActionContext<SitesState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
