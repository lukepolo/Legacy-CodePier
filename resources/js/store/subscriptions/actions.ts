import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SubscriptionsState } from "./stateInterface";
import SubscriptionService from "../../app/services/SubscriptionService";

export default function(subscriptionService: SubscriptionService) {
  return {
    plans: (context: ActionContext<SubscriptionsState, RootState>) => {
      return subscriptionService.plans().then(({ data }) => {
        context.commit("SET_PLANS", data);
        return data;
      });
    },
  };
}
