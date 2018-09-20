import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { NotificationsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<NotificationsState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
