import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class ServerFeatureStore extends RestStoreModule {
  constructor(@inject("ServerFeatureService") serverFeatureService) {
    super(serverFeatureService, "features");
    this.setName("features")
      .addState(state)
      .addActions(actions(serverFeatureService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
