import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteFireWallRuleStore extends RestStoreModule {
  constructor(@inject("SiteFirewallService") siteFirewallService) {
    super(siteFirewallService, "firewall_rules");
    this.setName("firewallRules")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
