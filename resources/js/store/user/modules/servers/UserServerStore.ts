import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";
import UserServerProviderStore from "@store/user/modules/servers/modules/providers/UserServerProviderStore";

@injectable()
export default class UserServerStore extends RestStoreModule {
  constructor(@inject("UserServerService") userServerService) {
    super(userServerService, "server");
    this.setName("servers")
      .addState(state)
      .addActions(actions(userServerService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserServerProviderStore);
  }
}
