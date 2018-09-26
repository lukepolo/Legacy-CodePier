/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/
import middleware from "./middleware";
import RouterInterface from "varie/lib/routing/RouterInterface";

import SiteViews from "@views/site";
import UserViews from "@views/user";
import LoginViews from "@views/login";
import ServerViews from "@views/server";
import Dashboard from "@views/dashboard/Dashboard.vue";
import ErrorViews from "@views/errors";

export default function($router: RouterInterface) {
  $router.route("/provider/:provider/callback", LoginViews.Oauth);

  // AUTHED
  $router
    .layout("authed")
    .middleware([middleware.Auth])
    .group(() => {
      $router.route("", Dashboard).setName("dashboard");

      $router.area(UserViews.AccountArea).group(() => {
        $router
          .route("my-account", UserViews.MyAccount)
          .setName("user.account");
        $router
          .route("subscription", UserViews.Subscription)
          .setName("user.subscription");
        $router
          .route("privacy", UserViews.PrivacySettings)
          .setName("user.privacy");
        $router.route("ssh-keys", UserViews.SshKeys).setName("user.ssh-keys");
        $router
          .route("server-providers", UserViews.ServerProviders)
          .setName("user.server-providers");
        $router
          .route("source-control-providers", UserViews.SourceControlProviders)
          .setName("user.source-control-providers");
        $router
          .route("notification-settings", UserViews.NotificationSettings)
          .setName("user.notification-settings");
      });

      $router
        .middleware([middleware.SiteWorkflowMustBeCompleted])
        .prefix("site/:site")
        .group(() => {
          $router.route("", SiteViews.SiteOverview).setName("site");
          $router.route("setup", SiteViews.SiteSetup).setName("site.setup");
        });

      $router.route("servers", ServerViews.Servers).setName("servers");
    });

  // PUBLIC
  $router.area(LoginViews.AuthArea).group(() => {
    $router.route("login", LoginViews.Login).setName("login");
    $router.route("register", LoginViews.Register).setName("register");
    $router
      .route("forgot-password", LoginViews.Register)
      .setName("forgotPassword");
    $router
      .route("reset-password", LoginViews.ResetPassword)
      .setName("resetPassword");
  });

  $router.route("*", ErrorViews.Error404);
}
