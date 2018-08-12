import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SshKeysState } from "./stateInterface";

export default function($http) {
  return {
    sampleAction: (context: ActionContext<SshKeysState, RootState>, data) => {
      return $http.post("/some-url", {
        data,
      });
    },
  };
}
