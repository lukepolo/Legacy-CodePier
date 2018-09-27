import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class ServerFrameworkStore extends StoreModule {
  constructor(@inject("ServerService") serverService) {
    super();
    this.setName("frameworks")
      .addState(state)
      .addActions(actions(serverService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
