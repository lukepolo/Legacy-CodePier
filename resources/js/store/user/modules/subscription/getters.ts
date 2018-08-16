import { SubscriptionState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SubscriptionState) => {
      return state;
    },
  };
}
