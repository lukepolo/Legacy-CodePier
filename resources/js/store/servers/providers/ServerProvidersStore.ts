import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class ServerProvidersStore extends RestStoreModule {
  constructor(
    @inject("HttpService") httpService,
    @inject("SystemServerProviderService") systemServerProviderService,
  ) {
    super(systemServerProviderService, "provider");
    this.setName("serverProviders")
      .addState(state)
      .addActions(actions(httpService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
