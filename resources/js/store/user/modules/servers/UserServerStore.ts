import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";
import UserServerProviderStore from "@store/user/modules/servers/modules/UserServerProviderStore";

@injectable()
export default class UserServerStore extends StoreModule {
  constructor(@inject("HttpService") httpService) {
    super();
    this.setName("server")
      .addState(state)
      .addActions(actions(httpService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserServerProviderStore);
  }
}
