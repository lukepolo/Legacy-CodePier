import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ServicesState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<ServicesState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
