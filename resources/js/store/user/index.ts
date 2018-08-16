import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";

@injectable()
export default class User extends StoreModule {
  constructor(@inject("UserService") userService) {
    super();
    this.setName("User")
      .addState(state)
      .addActions(actions(userService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
