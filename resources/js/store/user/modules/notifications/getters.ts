import { NotificationsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: NotificationsState) => {
      return state;
    },
  };
}
