import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";
import NotificationSettingStore from "@store/notifications/modules/settings/NotificationSettingStore";

@injectable()
export default class NotificationStore extends StoreModule {
  constructor(@inject("HttpService") httpService) {
    super();
    this.setName("notification")
      .addState(state)
      .addActions(actions(httpService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(NotificationSettingStore);
  }
}
