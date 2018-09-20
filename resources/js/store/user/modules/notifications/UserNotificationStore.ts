import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";
import UserNotificationSettingStore from "@store/user/modules/notifications/modules/settings/UserNotificationSettingStore";
import UserNotificationProviderStore from "@store/user/modules/notifications/modules/providers/UserNotificationProviderStore";

@injectable()
export default class UserNotificationStore extends StoreModule {
  constructor(@inject("HttpService") httpService) {
    super();
    this.setName("notification")
      .addState(state)
      .addActions(actions(httpService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserNotificationProviderStore)
      .addModule(UserNotificationSettingStore);
  }
}
