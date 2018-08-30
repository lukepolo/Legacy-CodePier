import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSshKeyStore extends RestStoreModule {
  constructor(@inject("UserSshKeyService") userSshKeyService) {
    super(userSshKeyService, "ssh_key");
    this.setName("sshKeys")
      .addState(state)
      .addActions(actions)
      .addMutations(mutations)
      .addGetters(getters);
  }
}
