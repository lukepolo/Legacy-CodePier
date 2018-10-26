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
    update: (context: ActionContext<SubscriptionState, RootState>, form) => {
      return subscriptionService.updateSubscription(form).then(({ data }) => {
        context.dispatch("invoices");
        context.commit("SET_SUBSCRIPTION", data);
        context.dispatch("auth/getUser", {}, { root: true });
      });
    },
    cancel: (context: ActionContext<SubscriptionState, RootState>) => {
      return subscriptionService.cancelSubscription().then(({ data }) => {
        context.dispatch("invoices");
        context.commit("SET_SUBSCRIPTION", data);
        context.dispatch("auth/getUser", {}, { root: true });
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
    subscribe: (context: ActionContext<SubscriptionState, RootState>, form) => {
      return subscriptionService.subscribe(form).then(({ data }) => {
        context.dispatch("invoices");
        context.commit("SET_SUBSCRIPTION", data);
        context.dispatch("auth/getUser", {}, { root: true });
      });
    },
    downloadInvoice({}, invoice) {
      return subscriptionService.downloadInvoice(invoice);
    },
  };
}
