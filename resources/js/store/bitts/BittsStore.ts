import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class BittsStore extends RestStoreModule {
  constructor(@inject("BittService") bittService) {
    super(bittService, "bitt");
    this.setName("bitts")
      .addState(state)
      .addActions(actions(bittService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
