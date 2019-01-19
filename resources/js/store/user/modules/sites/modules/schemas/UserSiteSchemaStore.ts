import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";
import UserSiteSchemaUserStore from "@store/user/modules/sites/modules/schemas/modules/users/UserSiteSchemaUserStore";

@injectable()
export default class UserSiteSchemaStore extends RestStoreModule {
  constructor(@inject("SiteSchemaService") siteSchemaService) {
    super(siteSchemaService, "schemas");
    this.setName("schemas")
      .addState(state)
      .addActions(actions(siteSchemaService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserSiteSchemaUserStore);
  }
}
