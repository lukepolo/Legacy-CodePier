import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteStore extends RestStoreModule {
  constructor(@inject("SiteService") $siteService) {
    super($siteService, "site");
    this.setName("sites")
      .addState(state)
      .addActions(actions($siteService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
