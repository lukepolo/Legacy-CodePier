import middleware from "./middleware";
import RouterInterface from "varie/lib/routing/RouterInterface";

import SiteViews from "@views/site";
import SetupPages from "@views/common/setup-components";

export default function($router: RouterInterface) {
  $router
    .middleware([middleware.SiteWorkflow])
    .prefix("site/:site")
    .group(() => {
      $router.route("", SiteViews.SiteOverview);
      $router.route("workflow", SiteViews.SiteWorkflow);

      $router
        .area(SiteViews.SiteArea)
        .data({ subNav: SiteViews.SiteSetupNav })
        .group(() => {
          $router.prefix("site-setup").group(() => {
            $router
              .route("", SiteViews.SiteSetup)
              .setAlias("repository-information")
              .setName("site.repository-information");

            $router.route("deployment", SiteViews.SiteDeployment);
            $router.route("files", SiteViews.SiteFiles);
            $router.route("database-management", SetupPages.DatabaseManagement);
          });
        });

      $router
        .area(SiteViews.SiteArea)
        .data({ subNav: SiteViews.SecurityNav })
        .group(() => {
          $router.prefix("security").group(() => {
            $router
              .route("", SetupPages.SshKeys)
              .setAlias("ssh-keys")
              .setName("site.ssh-keys");

            $router.route("firewall-rules", SetupPages.FirewallRules);
            $router.route("ssl-certificates", SetupPages.SslCertificates);
          });
        });

      $router
        .area(SiteViews.SiteArea)
        .data({ subNav: SiteViews.ServerSetupNav })
        .group(() => {
          $router.prefix("server-setup").group(() => {
            $router
              .route("", SetupPages.EnvironmentVariables)
              .setAlias("environment-variables")
              .setName("site.environment-variables");

            $router.route("cron-jobs", SetupPages.CronJobs);
            $router.route("daemons", SetupPages.Daemons);
            $router.route("workers", SetupPages.Workers);
            $router.route("language-settings", SetupPages.LanguageSettings);
            $router.route("server-features", SetupPages.ServerFeatures);
          });
        });
    });
}
