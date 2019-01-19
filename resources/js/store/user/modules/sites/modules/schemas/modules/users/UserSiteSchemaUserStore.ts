import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteSchemaUserStore extends RestStoreModule {
  constructor(@inject("SiteSchemaUserService") siteSchemaUserService) {
    super(siteSchemaUserService, "schema_users");
    this.setName("users")
      .addState(state)
      .addActions(actions(siteSchemaUserService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
