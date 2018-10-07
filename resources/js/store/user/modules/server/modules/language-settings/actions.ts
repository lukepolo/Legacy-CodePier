import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { LanguageSettingsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<LanguageSettingsState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
