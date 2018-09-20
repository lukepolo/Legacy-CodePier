import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserNotificationSettingStore extends RestStoreModule {
  constructor(
    @inject("UserNotificationSettingService") userNotificationSettingService,
  ) {
    super(userNotificationSettingService, "setting");
    this.setName("settings")
      .addState(state)
      .addActions(actions(userNotificationSettingService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
