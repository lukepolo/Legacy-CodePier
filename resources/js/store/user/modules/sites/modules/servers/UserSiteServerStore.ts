import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";
import UserSiteServerFeatureStore from "@store/user/modules/sites/modules/servers/modules/features/UserSiteServerFeatureStore";

@injectable()
export default class UserSiteServerStore extends RestStoreModule {
  constructor(@inject("SiteServerService") siteServerService) {
    super(siteServerService, "servers");
    this.setName("servers")
      .addState(state)
      .addActions(actions)
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserSiteServerFeatureStore);
  }
}
