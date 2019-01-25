import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from '@app/extensions/RestStoreModule/RestStoreModule'

@injectable()
export default class UserSiteFileStore extends RestStoreModule {
  constructor(@inject("SiteFileService") siteFileService) {
    super(siteFileService, 'files');
    this.setName("files")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
