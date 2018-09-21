import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SettingsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<SettingsState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
