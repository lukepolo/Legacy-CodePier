import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";
import ServerProviderStore from "@store/server/modules/providers/ServerProviderStore";

@injectable()
export default class ServerStore extends StoreModule {
  constructor(@inject("HttpService") httpService) {
    super();
    this.setName("server")
      .addState(state)
      .addActions(actions(httpService))
      .addMutations(mutations)
      .addModule(ServerProviderStore)
      .addGetters(getters);
  }
}
