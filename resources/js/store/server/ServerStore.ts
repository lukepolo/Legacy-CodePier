import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";
import ServerLanguageStore from "@store/server/modules/languages/ServerLanguageStore";
import ServerProviderStore from "@store/server/modules/providers/ServerProviderStore";
import ServerFrameworkStore from "@store/server/modules/frameworks/ServerFrameworkStore";
import ServerFeatureStore from "@store/server/modules/features/ServerFeatureStore";

@injectable()
export default class ServerStore extends StoreModule {
  constructor(@inject("HttpService") httpService) {
    super();
    this.setName("server")
      .addState(state)
      .addActions(actions(httpService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(ServerProviderStore)
      .addModule(ServerLanguageStore)
      .addModule(ServerFrameworkStore)
      .addModule(ServerFeatureStore);
  }
}
