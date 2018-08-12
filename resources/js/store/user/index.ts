import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";

@injectable()
export default class User extends StoreModule {
  constructor() {
    super();
    this.setName("User")
      .addState(state)
      .addActions(actions)
      .addMutations(mutations)
      .addGetters(getters);
  }
}
