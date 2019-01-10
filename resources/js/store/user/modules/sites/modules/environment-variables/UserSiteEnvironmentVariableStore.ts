import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteEnvironmentVariableStore extends RestStoreModule {
  constructor(
    @inject("SiteEnvironmentVariableService") siteEnvironmentVariableService,
  ) {
    super(siteEnvironmentVariableService, "environment_variables");
    this.setName("environmentVariables")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
