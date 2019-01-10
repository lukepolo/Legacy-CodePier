import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteLanguageSettingStore extends RestStoreModule {
  constructor(
    @inject("SiteLanguageSettingService") siteLanguageSettingService,
  ) {
    super(siteLanguageSettingService, "language_settings");
    this.setName("language_settings")
      .addState(state)
      .addActions(actions(siteLanguageSettingService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
