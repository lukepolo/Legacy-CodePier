import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class CommandsStore extends StoreModule {
  constructor(@inject("UserService") userService) {
    super();
    this.setName("commands")
      .addState(state)
      .addActions(actions(userService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
