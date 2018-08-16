import { SubscriptionsState } from "./stateInterface";

export default function() {
  return {
    SET_PLANS: (state: SubscriptionsState, plans) => {
      state.plans = plans;
    },
  };
}
