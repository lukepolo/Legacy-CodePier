import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class ServerLanguageStore extends StoreModule {
  constructor(@inject("ServerService") serverService) {
    super();
    this.setName("languages")
      .addState(state)
      .addActions(actions(serverService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
