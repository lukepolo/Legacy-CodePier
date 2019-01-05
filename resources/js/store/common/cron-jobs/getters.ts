import { CronJobsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: CronJobsState) => {
      return state;
    },
  };
}
