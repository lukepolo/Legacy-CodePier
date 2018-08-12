import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class User extends StoreModule {
  constructor(@inject("$http") $http) {
    super();
    this.setName("User")
      .addState(state)
      .addActions(actions($http))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
