import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { DnsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<DnsState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
