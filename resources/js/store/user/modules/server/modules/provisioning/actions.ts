import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ProvisioningState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<ProvisioningState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
