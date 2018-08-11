let $router = $app.make<RouterInterface>("$router");
import RouterInterface from "varie/lib/routing/RouterInterface";

/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/

$router.route("/provider/:provider/callback", "oauth");

$router
  .layout("authed")
  .middleware(["Auth"])
  .group(() => {
    $router.route("/", "dashboard/Dashboard").setName("dashboard");
  });

$router.area("login/Login").group(() => {
  $router.route("/login", "login/Login").setName("login");
  $router.route("/register", "login/Login");
  $router.route("/forgot-password", "login/Login");
});

$router.route("*", "errors/404");
