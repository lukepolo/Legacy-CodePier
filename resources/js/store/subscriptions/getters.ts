import { SubscriptionsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SubscriptionsState) => {
      return state;
    },
  };
}
