import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import PileService from "@app/services/PileService";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class Piles extends RestStoreModule {
  constructor(@inject("PileService") $pileService: PileService) {
    super($pileService, "piles");
    this.setName("Piles")
      .addState(state)
      .addActions(actions($pileService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
