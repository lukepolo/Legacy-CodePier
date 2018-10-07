import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import RestStoreModule from "../../app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class BuoysStore extends RestStoreModule {
  constructor(@inject("BuoyService") buoyService) {
    super(buoyService, "buoy");
    this.setName("buoys")
      .addState(state)
      .addActions(actions(buoyService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
