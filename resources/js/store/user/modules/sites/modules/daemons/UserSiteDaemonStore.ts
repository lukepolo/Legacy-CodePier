import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from '@app/extensions/RestStoreModule/RestStoreModule'

@injectable()
export default class UserSiteDaemonStore extends RestStoreModule {
  constructor(@inject("SiteDaemonService") siteDaemonService) {
    super(siteDaemonService, 'daemons');
    this.setName("daemons")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
