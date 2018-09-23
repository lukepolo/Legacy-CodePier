import actions from "./restStoreActions";
import getters from "./restStoreGetters";
import mutations from "./restStoreMutations";

import { injectable, unmanaged } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";

@injectable()
export default class RestStoreModule extends StoreModule {
  constructor($service, @unmanaged() stateName) {
    super();
    this.addActions(actions($service, stateName))
      .addMutations(mutations(stateName))
      .addGetters(getters(stateName));
  }
}
