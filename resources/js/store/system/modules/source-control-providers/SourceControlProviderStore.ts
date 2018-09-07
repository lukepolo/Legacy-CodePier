import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class SourceControlProviderStore extends RestStoreModule {
  constructor(
    @inject("SystemSourceControlProviderService")
    systemSourceControlProviderService,
  ) {
    super(systemSourceControlProviderService, "provider");
    this.setName("sourceControlProviders")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
