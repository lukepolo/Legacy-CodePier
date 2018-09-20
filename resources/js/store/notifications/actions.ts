import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { NotificationState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<NotificationState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
