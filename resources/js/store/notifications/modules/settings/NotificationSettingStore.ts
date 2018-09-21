import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class NotificationSettingStore extends StoreModule {
  constructor(
    @inject("NotificationSettingService") notificationSettingService,
  ) {
    super();
    this.setName("settings")
      .addState(state)
      .addActions(actions(notificationSettingService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
