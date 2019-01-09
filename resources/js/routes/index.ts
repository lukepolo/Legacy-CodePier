/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/
import middleware from "./middleware";
import RouterInterface from "varie/lib/routing/RouterInterface";

import UserViews from "@views/user";
import ErrorViews from "@views/errors";
import ServerViews from "@views/server";
import Dashboard from "@views/dashboard/Dashboard.vue";

import authRoutes from "./authRoutes";
import siteRoutes from "./siteRoutes";

export default function($router: RouterInterface) {
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

      siteRoutes($router);

      $router.route("servers", ServerViews.Servers);
    });

  authRoutes($router);

  $router.route("*", ErrorViews.Error404);
}
