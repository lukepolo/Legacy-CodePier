import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from '@app/extensions/RestStoreModule/RestStoreModule'

@injectable()
export default class UserSiteDeploymentStore extends RestStoreModule {
  constructor(@inject("SiteDeploymentService") siteDeploymentService) {
    super(siteDeploymentService, 'deployments');
    this.setName("deployments")
      .addState(state)
      .addActions(actions(siteDeploymentService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
