let $router = $app.make<RouterInterface>("$router");
import RouterInterface from "varie/lib/routing/RouterInterface";

/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/

$router.route("/provider/:provider/callback", "oauth");

// AUTHED
$router
  .layout("authed")
  .middleware(["Auth"])
  .group(() => {
    $router.route("/", "dashboard/Dashboard").setName("dashboard");

    $router.area("user/AccountArea").group(() => {
      $router.route("my-account", "user/MyAccount").setName("my_account");
      $router
        .route("subscription", "user/Subscription")
        .setName("user_subscription");
      $router.route("ssh-keys", "user/SshKeys").setName("user_ssh_keys");
      $router
        .route("server-providers", "user/ServerProviders")
        .setName("user_server_providers");
      $router
        .route("source-control", "user/SourceControlProviders")
        .setName("user_source_control_providers");
      $router
        .route("notification", "user/NotificationSettings")
        .setName("user_notification_settings");
      $router.route("privacy", "user/PrivacySettings").setName("user_privacy");
    });
  });

// PUBLIC
$router.area("login/AuthArea").group(() => {
  $router.route("login", "login/Login").setName("login");
  $router.route("register", "login/Register").setName("register");
  $router
    .route("forgot-password", "login/ForgotPassword")
    .setName("forgotPassword");
  $router
    .route("reset-password", "login/ResetPassword")
    .setName("resetPassword");
});

$router.route("*", "errors/404");
