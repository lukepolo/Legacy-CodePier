import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";
import UserSiteServerStore from "@store/user/modules/sites/modules/servers/UserSiteServerStore";
import UserSiteLifeLinesStore from "@store/user/modules/sites/modules/life-lines/UserSiteLifeLinesStore";

@injectable()
export default class UserSiteStore extends RestStoreModule {
  constructor(@inject("UserSiteService") userSiteService) {
    super(userSiteService, "site");
    this.setName("sites")
      .addState(state)
      .addActions(actions(userSiteService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserSiteServerStore)
      .addModule(UserSiteLifeLinesStore);
  }
}
