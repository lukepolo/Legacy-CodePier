import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";
import NotificationProviderService from "@app/services/Notification/NotificationProviderService";

@injectable()
export default class NotificationProviderStore extends RestStoreModule {
  constructor(
    @inject("NotificationProviderService") notificationProviderService,
  ) {
    super(notificationProviderService, "provider");
    this.setName("providers")
      .addState(state)
      .addActions(actions(notificationProviderService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
