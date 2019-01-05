import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { CronJobsState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (context: ActionContext<CronJobsState, RootState>, data) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
