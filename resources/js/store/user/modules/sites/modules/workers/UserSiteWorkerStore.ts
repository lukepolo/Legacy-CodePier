import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from '@app/extensions/RestStoreModule/RestStoreModule'

@injectable()
export default class UserSiteWorkerStore extends RestStoreModule {
  constructor(@inject("SiteWorkerService") siteWorkerService) {
    super(siteWorkerService, "workers");
    this.setName("workers")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
