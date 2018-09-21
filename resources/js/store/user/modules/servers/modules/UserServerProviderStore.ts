import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserServerProviderStore extends RestStoreModule {
  constructor(@inject("UserServerProviderService") userServerProviderService) {
    super(userServerProviderService, "provider");
    this.setName("provider")
      .addState(state)
      .addActions(actions(userServerProviderService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
