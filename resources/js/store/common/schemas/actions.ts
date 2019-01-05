import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SchemasState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<SchemasState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
