import { NotificationState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: NotificationState) => {
      return state;
    },
  };
}
