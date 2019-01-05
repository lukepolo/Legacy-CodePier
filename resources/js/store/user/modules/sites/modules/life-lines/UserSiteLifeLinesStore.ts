import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import UserSiteLifeLineService from "@app/services/User/UserSiteLifeLineService";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";
@injectable()
export default class UserSiteLifeLinesStore extends RestStoreModule {
  constructor(@inject("UserSiteLifeLineService") userSiteLifeLineService) {
    super(userSiteLifeLineService, "lifeline");
    this.setName("lifelines")
      .addState(state)
      .addActions(actions(userSiteLifeLineService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
