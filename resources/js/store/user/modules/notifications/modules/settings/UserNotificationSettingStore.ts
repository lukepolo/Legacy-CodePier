import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class UserNotificationSettingStore extends StoreModule {
  constructor(
    @inject("UserNotificationSettingService") userNotificationSettingService,
  ) {
    super();
    this.setName("settings")
      .addState(state)
      .addActions(actions(userNotificationSettingService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
