import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSourceControlProviderStore extends RestStoreModule {
  constructor(
    @inject("UserSourceControlProviderService")
    userSourceControlProviderService,
    @inject("OauthService") oauthService,
  ) {
    super(userSourceControlProviderService, "provider");
    this.setName("sourceControlProviders")
      .addState(state)
      .addActions(actions(userSourceControlProviderService, oauthService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
