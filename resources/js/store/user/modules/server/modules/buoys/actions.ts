import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { BuoysState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<BuoysState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
