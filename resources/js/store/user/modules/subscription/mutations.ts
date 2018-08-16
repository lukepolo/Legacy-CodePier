import { SubscriptionState } from "./stateInterface";

export default function() {
  return {
    SET_SUBSCRIPTION: (state: SubscriptionState, subscription) => {
      state.subscription = subscription;
    },
    SET_INVOICES: (state: SubscriptionState, invoices) => {
      state.invoices = invoices;
    },
  };
}
