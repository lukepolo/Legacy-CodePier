import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SshKeysState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<SshKeysState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
