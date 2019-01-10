import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";
import UserSiteServerStore from "@store/user/modules/sites/modules/servers/UserSiteServerStore";
import UserSiteLifeLinesStore from "@store/user/modules/sites/modules/life-lines/UserSiteLifeLinesStore";
import UserSiteDeploymentStore from "@store/user/modules/sites/modules/deployments/UserSiteDeploymentStore";
import UserSiteSshKeyStore from "@store/user/modules/sites/modules/ssh-keys/UserSiteSshKeyStore";
import UserSiteFireWallRuleStore from "@store/user/modules/sites/modules/firewall-rules/UserSiteFireWallRuleStore";
import UserSiteSslCertificateStore from "@store/user/modules/sites/modules/ssl-certificates/SslCertificatesStore";
import UserSiteEnvironmentVariableStore from "@store/user/modules/sites/modules/environment-variables/UserSiteEnvironmentVariableStore";
import UserSiteCronJobStore from "@store/user/modules/sites/modules/cron-jobs/UserSiteCronJobStore";

@injectable()
export default class UserSiteStore extends RestStoreModule {
  constructor(@inject("UserSiteService") userSiteService) {
    super(userSiteService, "site");
    this.setName("sites")
      .addState(state)
      .addActions(actions(userSiteService))
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(UserSiteServerStore)
      .addModule(UserSiteLifeLinesStore)
      .addModule(UserSiteDeploymentStore)
      .addModule(UserSiteSshKeyStore)
      .addModule(UserSiteFireWallRuleStore)
      .addModule(UserSiteSslCertificateStore)
      .addModule(UserSiteEnvironmentVariableStore)
      .addModule(UserSiteCronJobStore);
  }
}
