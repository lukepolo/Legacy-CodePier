import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteSshKeyStore extends RestStoreModule {
  constructor(@inject("SiteSshKeyService") siteSshKeyService) {
    super(siteSshKeyService, "ssh_keys");
    this.setName("sshKeys")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
