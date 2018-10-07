import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { FilesState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<FilesState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
