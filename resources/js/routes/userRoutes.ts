import UserViews from "@views/user";
import RouterInterface from "varie/lib/routing/RouterInterface";

export default function($router: RouterInterface) {
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
      $router.route("notification-settings", UserViews.NotificationSettings);
    });
}
