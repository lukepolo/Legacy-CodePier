import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { MembersState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<MembersState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
