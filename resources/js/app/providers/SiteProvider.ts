import SiteService from "../services/SiteService";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import SiteServerService from "../services/Site/SiteServerService";
import UserSiteLifeLineService from "@app/services/User/UserSiteLifeLineService";
import SiteDeploymentService from "@app/services/Site/SiteDeploymentService";
import SiteSshKeyService from "@app/services/Site/SiteSshKeyService";
import SiteFirewallService from "@app/services/Site/SiteFirewallService";
import SiteSslCertificateService from "@app/services/Site/SiteSslCertificateService";
import SiteEnvironmentVariableService from "@app/services/Site/SiteEnvironmentVariableService";
import SiteCronJobService from "@app/services/Site/SiteCronJobService";
import SiteDaemonService from "@app/services/Site/SiteDaemonService";
import SiteWorkerService from "@app/services/Site/SiteWorkerService";
import SiteLanguageSettingService from "@app/services/Site/SiteLanguageSettingService";
import SiteDeploymentStepService from "@app/services/Site/SiteDeploymentStepService";
import SiteServerFeatureService from "@app/services/Site/SiteServerFeatureService";

export default class SiteProvider extends ServiceProvider {
  public register() {
    this.app.bind("SiteService", SiteService);
    this.app.bind("SiteDaemonService", SiteDaemonService);
    this.app.bind("SiteServerService", SiteServerService);
    this.app.bind("SiteSshKeyService", SiteSshKeyService);
    this.app.bind("SiteWorkerService", SiteWorkerService);
    this.app.bind("SiteCronJobService", SiteCronJobService);
    this.app.bind("SiteFirewallService", SiteFirewallService);
    this.app.bind("SiteDeploymentService", SiteDeploymentService);
    this.app.bind("SiteDeploymentStepService", SiteDeploymentStepService);
    this.app.bind("SiteSslCertificateService", SiteSslCertificateService);
    this.app.bind("SiteLanguageSettingService", SiteLanguageSettingService);
    this.app.bind(
      "SiteEnvironmentVariableService",
      SiteEnvironmentVariableService,
    );
    this.app.bind("SiteServerFeatureService", SiteServerFeatureService);

    // TODO - this needs to be changed
    this.app.bind("UserSiteLifeLineService", UserSiteLifeLineService);
  }
}
