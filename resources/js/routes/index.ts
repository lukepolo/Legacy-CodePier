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
import ErrorViews from "@views/errors";
import ServerViews from "@views/server";
import Dashboard from "@views/dashboard/Dashboard.vue";

export default function($router: RouterInterface) {
  $router.route("/provider/:provider/callback", LoginViews.Oauth);

  // AUTHED
  $router
    .layout("authed")
    .middleware([middleware.Auth])
    .group(() => {
      $router.route("", Dashboard).setName("dashboard");

      $router
        .area(UserViews.AccountArea)
        .prefix("my")
        .group(() => {
          $router.route("account", UserViews.MyAccount);
          $router.route("subscription", UserViews.Subscription);
          $router.route("privacy", UserViews.PrivacySettings);
          $router.route("ssh-keys", UserViews.SshKeys);
          $router.route("server-providers", UserViews.ServerProviders);
          $router.route(
            "source-control-providers",
            UserViews.SourceControlProviders,
          );
          $router.route(
            "notification-settings",
            UserViews.NotificationSettings,
          );
        });

      $router
        .middleware([middleware.SiteWorkflowMustBeCompleted])
        .prefix("site/:site")
        .group(() => {
          $router.route("", SiteViews.SiteOverview);
          $router.route("setup", SiteViews.SiteSetup);
          $router.route("workflow", SiteViews.SiteWorkflow);
        });

      $router.route("servers", ServerViews.Servers);
    });

  // PUBLIC
  $router.area(LoginViews.AuthArea).group(() => {
    $router.route("login", LoginViews.Login);
    $router.route("register", LoginViews.Register);
    $router.route("forgot-password", LoginViews.Register);
    $router.route("reset-password", LoginViews.ResetPassword);
  });

  $router.route("*", ErrorViews.Error404);
}
