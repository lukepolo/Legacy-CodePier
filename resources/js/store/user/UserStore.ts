import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";
import UserPileStore from "@store/user/modules/piles/UserPileStore";
import UserSiteStore from "@store/user/modules/sites/UserSiteStore";
import UserSshKeyStore from "@store/user/modules/ssh_keys/UserSshKeyStore";
import UserSubscriptionStore from "@store/user/modules/subscription/UserSubscriptionStore";

@injectable()
export default class UserStore extends StoreModule {
  constructor(@inject("UserService") userService) {
    super();
    this.setName("User")
      .addState(state)
      .addActions(actions(userService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserPileStore)
      .addModule(UserSiteStore)
      .addModule(UserSshKeyStore)
      .addModule(UserSubscriptionStore);
  }
}
