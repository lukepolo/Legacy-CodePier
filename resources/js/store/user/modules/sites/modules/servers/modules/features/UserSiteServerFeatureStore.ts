import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteServerFeatureStore extends RestStoreModule {
  constructor(@inject("SiteServerFeatureService") siteServerFeatureService) {
    super(siteServerFeatureService, "features");
    this.setName("features")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
