import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserNotificationProviderStore extends RestStoreModule {
  constructor(
    @inject("UserNotificationProviderService") userNotificationProviderService,
    @inject("OauthService") oauthService,
  ) {
    super(userNotificationProviderService, "provider");
    this.setName("provider")
      .addState(state)
      .addActions(actions(userNotificationProviderService, oauthService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
