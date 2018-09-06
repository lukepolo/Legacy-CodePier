import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class UserSubscriptionStore extends StoreModule {
  constructor(@inject("SubscriptionService") subscriptionService) {
    super();
    this.setName("subscription")
      .addState(state)
      .addActions(actions(subscriptionService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
