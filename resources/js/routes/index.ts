/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/
import RouterInterface from "varie/lib/routing/RouterInterface";

import UserViews from "@views/user";
import LoginViews from "@views/login";
import Dashboard from "@views/dashboard/Dashboard.vue";

import ErrorViews from "@views/errors";

export default function($router: RouterInterface) {
  $router.route("/provider/:provider/callback", LoginViews.Oauth);

  // AUTHED
  $router
    .layout("authed")
    .middleware(["Auth"])
    .group(() => {
      $router.route("/", Dashboard).setName("dashboard");

      $router.area(UserViews.AccountArea).group(() => {
        $router.route("my-account", UserViews.MyAccount).setName("my_account");
        $router
          .route("subscription", UserViews.Subscription)
          .setName("user_subscription");
        $router
          .route("privacy", UserViews.PrivacySettings)
          .setName("user_privacy");
      });
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
