import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SubscriptionState } from "./stateInterface";
import SubscriptionService from "@app/services/SubscriptionService";

export default function(subscriptionService: SubscriptionService) {
  return {
    get: (context: ActionContext<SubscriptionState, RootState>) => {
      return subscriptionService.getCurrentSubscription().then(({ data }) => {
        context.commit("SET_SUBSCRIPTION", data);
      });
    },
    plans: (context: ActionContext<SubscriptionState, RootState>) => {
      return subscriptionService.plans().then(({ data }) => {
        context.commit("SET_PLANS", data);
      });
    },
    invoices: (context: ActionContext<SubscriptionState, RootState>) => {
      return subscriptionService.invoices().then(({ data }) => {
        context.commit("SET_INVOICES", data);
      });
    },
  };
}
