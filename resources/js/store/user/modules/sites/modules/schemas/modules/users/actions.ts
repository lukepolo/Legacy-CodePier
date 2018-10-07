import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { UsersState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<UsersState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
