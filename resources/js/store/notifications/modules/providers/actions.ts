import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ProvidersState } from "./stateInterface";

export default function(notificationProviderService) {
  return {
    SAMPLE: (context: ActionContext<ProvidersState, RootState>, data) => {},
  };
}
